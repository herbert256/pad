<?php
  // Native single-segment rMQR. Tables and layout adapted from Zint BSD-3-Clause;
  // provenance and retained license: SPECIALIZED-CODES-LICENSE.
  include_once PAD . 'subs/codes/_lib/qr.php';

  function padRmqrEncode ( $text, $options = [] ) {
    if ( ! is_string ( $text ) || $text === '' || strlen ( $text ) > 4096 || ! is_array ( $options ) ) return 'rMQR requires nonempty text of at most 4096 bytes and an options array';
    foreach ( $options as $key => $value ) if ( ! in_array ( $key, [ 'version', 'level', 'mode', 'encoding' ], TRUE ) ) return 'unknown rMQR option';
    $version = $options ['version'] ?? 'auto'; $level = $options ['level'] ?? 'M'; $mode = $options ['mode'] ?? 'auto'; $encoding = $options ['encoding'] ?? 'ISO-8859-1';
    if ( ! is_string ( $version ) || ! in_array ( $level, [ 'M', 'H' ], TRUE ) || ! in_array ( $mode, [ 'auto', 'numeric', 'alnum', 'byte' ], TRUE ) || ! in_array ( $encoding, [ 'ISO-8859-1', 'UTF-8', 'binary' ], TRUE ) ) return 'invalid rMQR version, level, mode or encoding';
    if ( $encoding !== 'binary' && ! mb_check_encoding ( $text, 'UTF-8' ) ) return 'rMQR text must be valid UTF-8';
    [ $detected ] = padQrSegment ( $text ); if ( $mode === 'auto' ) $mode = $detected;
    $payload = ''; $prefix = ''; $bytes = $text;
    if ( $mode === 'byte' ) {
      if ( $encoding === 'ISO-8859-1' ) { $bytes = mb_convert_encoding ( $text, 'ISO-8859-1', 'UTF-8' ); if ( mb_convert_encoding ( $bytes, 'UTF-8', 'ISO-8859-1' ) !== $text ) return 'rMQR text cannot be represented in ISO-8859-1'; }
      if ( $encoding === 'UTF-8' ) $prefix = '11100011010'; // ECI 26, then byte segment.
      foreach ( str_split ( $bytes ) as $byte ) $payload .= sprintf ( '%08b', ord ( $byte ) );
      $count = strlen ( $bytes );
    } elseif ( $mode === 'numeric' ) {
      if ( ! ctype_digit ( $text ) ) return 'rMQR numeric mode accepts digits only';
      [ , , $count, $payload ] = padQrSegment ( $text );
    } else {
      if ( strspn ( $text, PAD_QR_ALNUM ) !== strlen ( $text ) ) return 'rMQR alnum mode accepts the QR alphanumeric alphabet only';
      $count = strlen ( $text );
      foreach ( str_split ( $text, 2 ) as $pair ) $payload .= strlen ( $pair ) === 2 ? sprintf ( '%011b', strpos ( PAD_QR_ALNUM, $pair [0] ) * 45 + strpos ( PAD_QR_ALNUM, $pair [1] ) ) : sprintf ( '%06b', strpos ( PAD_QR_ALNUM, $pair ) );
    }
    static $table; $table ??= include PAD . 'subs/codes/_lib/rmqr-tables.php';
    $chosen = NULL; $footprint = PHP_INT_MAX; $known = $version === 'auto'; $ecc = $level === 'H' ? 1 : 0;
    $countTable = [ 'numeric' => 'numeric_cci', 'alnum' => 'alphanum_cci', 'byte' => 'byte_cci' ] [$mode];
    for ( $v = 0; $v < 32; $v++ ) {
      $name = 'R' . $table ['height'] [$v] . 'x' . $table ['width'] [$v];
      if ( $version !== 'auto' && $version !== $name ) continue; $known = TRUE;
      $cci = $table [$countTable] [$v]; $capacity = 8 * $table ['data_codewords'] [$ecc] [$v]; $area = $table ['height'] [$v] * $table ['width'] [$v];
      if ( $count < ( 1 << $cci ) && strlen ( $prefix ) + 3 + $cci + strlen ( $payload ) <= $capacity && $area < $footprint ) { $chosen = $v; $footprint = $area; }
    }
    if ( ! $known ) return 'unknown rMQR version (use auto or an exact Rheightxwidth name)';
    if ( $chosen === NULL ) return 'payload does not fit the selected rMQR version and correction level';
    $v = $chosen; $width = $table ['width'] [$v]; $height = $table ['height'] [$v]; $capacity = 8 * $table ['data_codewords'] [$ecc] [$v];
    $bits = $prefix . sprintf ( '%03b', [ 'numeric' => 1, 'alnum' => 2, 'byte' => 3 ] [$mode] ) . sprintf ( '%0' . $table [$countTable] [$v] . 'b', $count ) . $payload;
    $bits .= str_repeat ( '0', min ( 3, $capacity - strlen ( $bits ) ) );
    $bits .= str_repeat ( '0', min ( ( 8 - strlen ( $bits ) % 8 ) % 8, $capacity - strlen ( $bits ) ) );
    $data = array_map ( 'bindec', str_split ( $bits, 8 ) );
    for ( $pad = 0; count ( $data ) < $capacity / 8; $pad++ ) $data [] = $pad % 2 ? 0x11 : 0xEC;
    $blocks = $table ['blocks'] [$ecc] [$v]; $total = $table ['total_codewords'] [$v]; $short = intdiv ( count ( $data ), $blocks ); $shortCount = $blocks - count ( $data ) % $blocks;
    $errorCount = intdiv ( $total - count ( $data ), $blocks ); $divisor = padQrDivisor ( $errorCount ); $dataBlocks = $errorBlocks = []; $offset = 0;
    for ( $block = 0; $block < $blocks; $block++ ) { $length = $short + ( $block >= $shortCount ? 1 : 0 ); $dataBlocks [] = array_slice ( $data, $offset, $length ); $errorBlocks [] = padQrRemainder ( $dataBlocks [$block], $divisor ); $offset += $length; }
    $codewords = [];
    for ( $i = 0; $i <= $short; $i++ ) for ( $block = 0; $block < $blocks; $block++ ) if ( isset ( $dataBlocks [$block] [$i] ) ) $codewords [] = $dataBlocks [$block] [$i];
    for ( $i = 0; $i < $errorCount; $i++ ) for ( $block = 0; $block < $blocks; $block++ ) $codewords [] = $errorBlocks [$block] [$i];
    $grid = padRmqrGrid ( $width, $height, $table ); $stream = ''; foreach ( $codewords as $word ) $stream .= sprintf ( '%08b', $word );
    $at = 0; $up = TRUE;
    for ( $right = $width - 2; $right > 0; $right -= 2 ) {
      for ( $row = 0; $row < $height; $row++ ) { $y = $up ? $height - 1 - $row : $row;
        foreach ( [ $right, $right - 1 ] as $x ) if ( $grid [$y] [$x] === NULL ) { $grid [$y] [$x] = ( $at < strlen ( $stream ) ? (int) $stream [$at++] : 0 ) ^ ( ( intdiv ( $y, 2 ) + intdiv ( $x, 3 ) ) % 2 === 0 ? 1 : 0 ); }
      }
      $up = ! $up;
    }
    if ( $at !== strlen ( $stream ) ) return 'internal rMQR placement capacity mismatch';
    $format = $v + ( $ecc ? 32 : 0 ); $left = $table ['format_info_left'] [$format]; $right = $table ['format_info_right'] [$format];
    for ( $i = 0; $i < 5; $i++ ) for ( $j = 0; $j < 3; $j++ ) { $grid [$i + 1] [$j + 8] = ( $left >> ( $j * 5 + $i ) ) & 1; $grid [$i + $height - 6] [$j + $width - 8] = ( $right >> ( $j * 5 + $i ) ) & 1; }
    for ( $i = 0; $i < 3; $i++ ) { $grid [$i + 1] [11] = ( $left >> ( 15 + $i ) ) & 1; $grid [$height - 6] [$width - 5 + $i] = ( $right >> ( 15 + $i ) ) & 1; }
    return [ 'matrix' => $grid, 'version' => "R{$height}x{$width}", 'level' => $level, 'mode' => $mode, 'encoding' => $encoding, 'codewords' => $codewords ];
  }

  function padRmqrGrid ( $width, $height, $table ) {
    $grid = array_fill ( 0, $height, array_fill ( 0, $width, NULL ) );
    for ( $x = 0; $x < $width; $x++ ) $grid [0] [$x] = $grid [$height - 1] [$x] = 1 - $x % 2;
    for ( $y = 0; $y < $height; $y++ ) $grid [$y] [0] = $grid [$y] [$width - 1] = 1 - $y % 2;
    for ( $y = 0; $y < 7; $y++ ) for ( $x = 0; $x < 7; $x++ ) $grid [$y] [$x] = (int) ( $x === 0 || $x === 6 || $y === 0 || $y === 6 || ( $x >= 2 && $x <= 4 && $y >= 2 && $y <= 4 ) );
    for ( $y = 0; $y < 5; $y++ ) for ( $x = 0; $x < 5; $x++ ) $grid [$height - 5 + $y] [$width - 5 + $x] = (int) ( $x === 0 || $x === 4 || $y === 0 || $y === 4 || ( $x === 2 && $y === 2 ) );
    $grid [$height - 2] [0] = 1; $grid [$height - 2] [1] = 0; $grid [$height - 1] [1] = 1;
    $grid [0] [$width - 2] = 1; $grid [1] [$width - 2] = 0; $grid [1] [$width - 1] = 1;
    for ( $y = 0; $y < 7; $y++ ) $grid [$y] [7] = 0;
    if ( $height > 7 ) for ( $x = 0; $x < 8; $x++ ) $grid [7] [$x] = 0;
    if ( $width > 27 ) {
      $positions = array_slice ( $table ['table_d1'], array_search ( $width, [ 43, 59, 77, 99, 139 ], TRUE ) * 4, 4 );
      foreach ( $positions as $x ) if ( $x ) {
        for ( $y = 0; $y < $height; $y++ ) $grid [$y] [$x] = 1 - $y % 2;
        foreach ( [ 1, 2, $height - 3, $height - 2 ] as $y ) $grid [$y] [$x - 1] = $grid [$y] [$x + 1] = 1;
      }
    }
    for ( $i = 0; $i < 5; $i++ ) for ( $j = 0; $j < 3; $j++ ) { $grid [$i + 1] [$j + 8] = 0; $grid [$i + $height - 6] [$j + $width - 8] = 0; }
    for ( $i = 0; $i < 3; $i++ ) { $grid [$i + 1] [11] = 0; $grid [$height - 6] [$width - 5 + $i] = 0; }
    return $grid;
  }
?>
