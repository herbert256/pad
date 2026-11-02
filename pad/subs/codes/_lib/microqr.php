<?php
  // Native Micro QR M1-M4 encoder. Layout/masks checked against Segno and Zint;
  // ISO/IEC 18004:2015 sections 7.4, 7.6-7.9. Attribution in MICROQR-LICENSE.
  // One segment, selected automatically or explicitly. Shared QR code is used only for
  // numeric/alphanumeric payload bits and GF(256) Reed-Solomon arithmetic.
  include_once PAD . 'subs/codes/_lib/qr.php';

  function padMicroQrEncode ( $text, $options = [] ) {
    if ( ! is_string ( $text ) or $text === '' or strlen ( $text ) > 1000 or ! is_array ( $options ) )
      return 'Micro QR requires nonempty text (at most 1000 input bytes) and an options array';
    foreach ( $options as $key => $value )
      if ( ! in_array ( $key, ['version','level','mode','mask','encoding'], TRUE ) ) return 'unknown Micro QR option';
    $version = $options ['version'] ?? 'auto'; $level = $options ['level'] ?? 'auto';
    $mode = $options ['mode'] ?? 'auto'; $mask = $options ['mask'] ?? -1; $encoding = $options ['encoding'] ?? 'UTF-8';
    if ( ! in_array ( $version, ['auto','M1','M2','M3','M4'], TRUE ) or ! in_array ( $level, ['auto','none','L','M','Q'], TRUE )
         or ! in_array ( $mode, ['auto','numeric','alnum','byte','kanji'], TRUE ) or ! is_int ( $mask ) or $mask < -1 or $mask > 3
         or ! in_array ( $encoding, ['UTF-8','ISO-8859-1','SJIS','binary'], TRUE ) ) return 'invalid Micro QR version, correction level, mode, mask or encoding';
    if ( $encoding !== 'binary' and ! mb_check_encoding ( $text, 'UTF-8' ) ) return 'Micro QR text must be valid UTF-8';
    $kanji = padMicroQrKanji ( $text );
    if ( $mode === 'auto' ) {
      [ $detected ] = padQrSegment ( $text );
      $mode = ( $detected === 'byte' and $kanji !== NULL ) ? 'kanji' : $detected;
    }
    if ( $mode === 'kanji' ) {
      if ( $kanji === NULL ) return 'Micro QR Kanji mode requires characters in the QR Shift-JIS ranges';
      $count = count ( $kanji ); $payload = '';
      foreach ( $kanji as $value ) $payload .= sprintf ( '%013b', $value );
    } elseif ( $mode === 'byte' ) {
      $bytes = $text;
      if ( $encoding !== 'binary' and $encoding !== 'UTF-8' ) {
        $bytes = mb_convert_encoding ( $text, $encoding, 'UTF-8' );
        if ( mb_convert_encoding ( $bytes, 'UTF-8', $encoding ) !== $text ) return 'text is not representable in the selected Micro QR encoding';
      }
      $count = strlen ( $bytes ); $payload = '';
      foreach ( str_split ( $bytes ) as $byte ) $payload .= sprintf ( '%08b', ord ( $byte ) );
    } else {
      if ( $mode === 'numeric' and ! ctype_digit ( $text ) ) return 'Micro QR numeric mode accepts digits only';
      if ( $mode === 'alnum' and strspn ( $text, PAD_QR_ALNUM ) !== strlen ( $text ) ) return 'Micro QR alnum mode accepts the QR alphanumeric alphabet only';
      // Force alphanumeric encoding even when its text happens to contain digits only.
      if ( $mode === 'alnum' ) {
        $count = strlen ( $text ); $payload = '';
        foreach ( str_split ( $text, 2 ) as $pair )
          $payload .= strlen ( $pair ) === 2 ? sprintf ( '%011b', strpos ( PAD_QR_ALNUM, $pair [0] ) * 45 + strpos ( PAD_QR_ALNUM, $pair [1] ) ) : sprintf ( '%06b', strpos ( PAD_QR_ALNUM, $pair ) );
      } else [ , , $count, $payload ] = padQrSegment ( $text );
    }
    $capacities = [ 1=>['none'=>20], 2=>['L'=>40,'M'=>32], 3=>['L'=>84,'M'=>68], 4=>['L'=>128,'M'=>112,'Q'=>80] ];
    $modes = ['numeric'=>0,'alnum'=>1,'byte'=>2,'kanji'=>3];
    $minimum = ['numeric'=>1,'alnum'=>2,'byte'=>3,'kanji'=>3];
    $chosen = NULL;
    for ( $v = 1; $v <= 4; $v++ ) {
      if ( $version !== 'auto' and $version !== "M$v" ) continue;
      if ( $v < $minimum [$mode] ) continue;
      $ecc = $level === 'auto' ? ( $v === 1 ? 'none' : 'L' ) : $level;
      if ( ! isset ( $capacities [$v] [$ecc] ) ) continue;
      $countBits = $v + ( $mode === 'numeric' ? 2 : ( $mode === 'kanji' ? 0 : 1 ) );
      $length = $v - 1 + $countBits + strlen ( $payload );
      if ( $count < ( 1 << $countBits ) and $length <= $capacities [$v] [$ecc] ) { $chosen = [$v,$ecc,$countBits]; break; }
    }
    if ( $chosen === NULL ) return 'text/mode does not fit the selected Micro QR version and correction level';
    [ $v, $ecc, $countBits ] = $chosen; $capacity = $capacities [$v] [$ecc];
    $bits = ( $v > 1 ? sprintf ( '%0' . ( $v - 1 ) . 'b', $modes [$mode] ) : '' ) . sprintf ( '%0' . $countBits . 'b', $count ) . $payload;
    $bits .= str_repeat ( '0', min ( $capacity - strlen ( $bits ), 2 * $v + 1 ) );
    // Pad to the byte boundary, then alternate EC/11, with the final half codeword zero.
    $bits .= str_repeat ( '0', min ( $capacity - strlen ( $bits ), ( 8 - strlen ( $bits ) % 8 ) % 8 ) );
    $pad = 0;
    while ( strlen ( $bits ) + 8 <= $capacity ) $bits .= $pad++ % 2 ? '00010001' : '11101100';
    $bits .= str_repeat ( '0', $capacity - strlen ( $bits ) );
    $data = [];
    foreach ( str_split ( $bits, 8 ) as $byte ) $data [] = bindec ( str_pad ( $byte, 8, '0' ) );
    $errorWords = [1=>5,2=>10,3=>17,4=>24][$v] - count ( $data );
    $stream = $bits;
    foreach ( padQrRemainder ( $data, padQrDivisor ( $errorWords ) ) as $word ) $stream .= sprintf ( '%08b', $word );
    $size = 9 + 2 * $v; $matrix = array_fill ( 0, $size, array_fill ( 0, $size, NULL ) );
    for ( $y = 0; $y < 8; $y++ )
      for ( $x = 0; $x < 8; $x++ )
        $matrix [$y][$x] = (int) ( $x < 7 and $y < 7 and ( $x === 0 or $x === 6 or $y === 0 or $y === 6 or ( $x >= 2 and $x <= 4 and $y >= 2 and $y <= 4 ) ) );
    for ( $i = 8; $i < $size; $i++ ) { $matrix [0][$i] = (int) ( $i % 2 === 0 ); $matrix [$i][0] = (int) ( $i % 2 === 0 ); }
    for ( $i = 1; $i <= 8; $i++ ) { $matrix [8][$i] = 0; $matrix [$i][8] = 0; }
    $function = array_map ( fn ( $row ) => array_map ( fn ( $value ) => $value !== NULL, $row ), $matrix );
    $at = 0; $up = TRUE;
    for ( $right = $size - 1; $right > 0; $right -= 2, $up = ! $up )
      for ( $vertical = 0; $vertical < $size; $vertical++ )
        for ( $z = 0; $z < 2; $z++ ) {
          $x = $right - $z; $y = $up ? $size - 1 - $vertical : $vertical;
          if ( ! $function [$y][$x] ) $matrix [$y][$x] = (int) $stream [$at++];
        }
    if ( $at !== strlen ( $stream ) ) return 'Micro QR internal placement error';
    $best = NULL; $bestScore = -1; $bestMask = 0;
    foreach ( $mask < 0 ? [0,1,2,3] : [$mask] as $candidate ) {
      $trial = $matrix;
      for ( $y = 0; $y < $size; $y++ )
        for ( $x = 0; $x < $size; $x++ )
          if ( ! $function [$y][$x] ) {
            $flip = match ( $candidate ) {
              0 => $y % 2 === 0,
              1 => ( intdiv ( $y, 2 ) + intdiv ( $x, 3 ) ) % 2 === 0,
              2 => ( ( $x * $y ) % 2 + ( $x * $y ) % 3 ) % 2 === 0,
              3 => ( ( $x + $y ) % 2 + ( $x * $y ) % 3 ) % 2 === 0
            };
            if ( $flip ) $trial [$y][$x] ^= 1;
          }
      $row = $col = 0;
      for ( $i = 1; $i < $size; $i++ ) { $row += $trial [$size - 1][$i]; $col += $trial [$i][$size - 1]; }
      $score = 16 * min ( $row, $col ) + max ( $row, $col );
      if ( $score > $bestScore ) { $best = $trial; $bestScore = $score; $bestMask = $candidate; }
    }
    $formatClass = [1=>['none'=>0],2=>['L'=>1,'M'=>2],3=>['L'=>3,'M'=>4],4=>['L'=>5,'M'=>6,'Q'=>7]][$v][$ecc];
    $format = ( $formatClass << 2 ) | $bestMask; $remainder = $format;
    for ( $i = 0; $i < 10; $i++ ) $remainder = ( $remainder << 1 ) ^ ( ( ( $remainder >> 9 ) & 1 ) * 0x537 );
    $format = ( ( $format << 10 ) | $remainder ) ^ 0x4445;
    for ( $i = 0; $i < 8; $i++ ) { $best [$i + 1][8] = ( $format >> $i ) & 1; $best [8][$i + 1] = ( $format >> ( 14 - $i ) ) & 1; }
    return [ 'matrix'=>$best, 'version'=>"M$v", 'level'=>$ecc, 'mode'=>$mode, 'mask'=>$bestMask ];
  }

  function padMicroQrKanji ( $text ) {
    if ( ! mb_check_encoding ( $text, 'UTF-8' ) ) return NULL;
    $sjis = mb_convert_encoding ( $text, 'SJIS', 'UTF-8' );
    if ( mb_convert_encoding ( $sjis, 'UTF-8', 'SJIS' ) !== $text or strlen ( $sjis ) % 2 ) return NULL;
    $values = [];
    foreach ( str_split ( $sjis, 2 ) as $pair ) {
      $code = ( ord ( $pair [0] ) << 8 ) | ord ( $pair [1] );
      if ( $code >= 0x8140 and $code <= 0x9FFC ) $code -= 0x8140;
      elseif ( $code >= 0xE040 and $code <= 0xEBBF ) $code -= 0xC140;
      else return NULL;
      $values [] = ( $code >> 8 ) * 0xC0 + ( $code & 255 );
    }
    return $values;
  }
?>
