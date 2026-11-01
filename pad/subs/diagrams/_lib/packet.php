<?php

  // The packet diagram - subs/diagrams/types/packet.php: the fields of a packet header, a
  // frame or a register, bit by bit, written as Mermaid's packet writes them.
  //
  //   title UDP header                   a heading over the rows
  //   0-15: "Source Port"                a field from bit 0 to bit 15
  //   +16: "Destination Port"            the next 16 bits
  //   32-47: "Length"
  //   48: "Flag"                         one bit
  //
  // The rows are 32 bits wide unless bits= says otherwise, bit 0 on the left, as the RFCs
  // draw a header; a field that runs past the end of its row goes on at the start of the
  // next, its name in every piece. Over every piece stand the numbers of its first and its
  // last bit, as many as fit. A name wider than its field is wrapped, and in a field too
  // narrow for a word it is turned on its side; a row is as high as its tallest field asks,
  // so no name runs out of its box.

  // bits= - the bits of a row: 32, else 4 to 64.

  function padDiagramPacketBits () {

    global $padCheckSyntax;

    $bits = trim ( (string) padTagParm ( 'bits', '32' ) );

    if ( ! ctype_digit ( $bits ) or (int) $bits < 4 or (int) $bits > 64 ) {
      if ( $padCheckSyntax )
        padError ( "the packet diagram has no bits '" . padMakeSafe ( $bits, 20 ) . "' - a row of 4 to 64 bits" );
      return 32;
    }

    return (int) $bits;

  }

  // The accessible name a packet has without title=: the heading its title line draws - the
  // last, without its comment - else Packet diagram.

  function padDiagramPacketTitle ( $source ) {

    $title = 'Packet diagram';

    foreach ( preg_split ( '/\r?\n/', padUnprotectQuotes ( (string) $source ) ) as $line )
      if ( preg_match ( '/^title\s+(.+)$/i', padDiagramPacketLine ( $line ), $m ) )
        $title = padDiagramFlat ( $m [1] );

    return $title;

  }

  // A line without its comment: a line that starts with %%, or %% after a space outside the
  // quotes and what follows it - a name "50 %% off" keeps its %%.

  function padDiagramPacketLine ( $line ) {

    if ( preg_match ( '/^\s*%%/', $line ) )
      return '';

    return trim ( preg_replace ( '/^((?:[^"]|"[^"]*")*?)\s%%.*$/s', '$1', $line ) ?? $line );

  }

  function padDiagramPacket ( $source, $bits, $title ) {

    global $padCheckSyntax;

    $fields  = $bad = [];
    $heading = '';
    $next    = 0;
    $first   = TRUE;

    foreach ( preg_split ( '/\r?\n/', padUnprotectQuotes ( (string) $source ) ) as $number => $line ) {

      $line = padDiagramPacketLine ( $line );

      if ( $line === '' )
        continue;

      if ( $first and in_array ( $line, [ 'packet', 'packet-beta' ] ) ) {
        $first = FALSE;
        continue;
      }

      $first = FALSE;

      if ( preg_match ( '/^title\s+(.+)$/i', $line, $m ) ) {
        $heading = padDiagramFlat ( $m [1] );
        continue;
      }

      if ( ! preg_match ( '/^(?:(\d+)(?:\s*-\s*(\d+))?|\+\s*(\d+))\s*:\s*(?:"([^"]*)"|(.*))$/u', $line, $m ) ) {
        $bad [] = [ $number + 1, $line, 'write a field as 0-15: "name", 16: "name" or +8: "name"' ];
        continue;
      }

      $name = padDiagramFlat ( ( $m [4] ?? '' ) !== '' ? $m [4] : trim ( $m [5] ?? '' ) );

      if ( ( $m [3] ?? '' ) !== '' ) {
        $size = (int) $m [3];
        if ( $size < 1 ) {
          $bad [] = [ $number + 1, $line, 'a field has 1 bit at least' ];
          continue;
        }
        $from = $next;
        $to   = $next + $size - 1;
      } else {
        $from = (int) $m [1];
        $to   = ( $m [2] ?? '' ) !== '' ? (int) $m [2] : $from;
      }

      if ( $to < $from ) {
        $bad [] = [ $number + 1, $line, "it ends at bit $to, before it starts at bit $from" ];
        continue;
      }

      if ( $from != $next ) {
        $bad [] = [ $number + 1, $line, "it starts at bit $from, and " . ( $next ? 'the fields before it end at bit ' . ( $next - 1 ) : 'the first field starts at bit 0' )
                                        . ' - the fields follow each other from bit 0' ];
        continue;
      }

      if ( $to >= 4096 ) {
        padError ( "the packet diagram is too large to lay out: its fields reach bit $to, 4095 at most - draw it in parts" );
        return '';
      }

      $fields [] = [ $from, $to, $name ];
      $next      = $to + 1;

    }

    if ( $bad and $padCheckSyntax )
      padError ( 'the packet diagram cannot read line ' . $bad [0] [0] . ": '" . padMakeSafe ( $bad [0] [1], 60 ) . "' - " . $bad [0] [2] );

    if ( ! $fields )
      return '';

    $bitW = max ( 14, min ( 48, 640 / $bits ) );
    $rows = (int) ceil ( $next / $bits );

    // The pieces: a field cut at the ends of the rows, with the way its name is written and
    // the height that asks for.

    $pieces = [];
    $high   = array_fill ( 0, $rows, 32 );

    foreach ( $fields as $f => list ( $from, $to, $name ) )
      for ( $bit = $from; $bit <= $to; $bit = $end + 1 ) {

        $row   = intdiv ( $bit, $bits );
        $end   = min ( $to, ( $row + 1 ) * $bits - 1 );
        $w     = ( $end - $bit + 1 ) * $bitW;
        $room  = $w - 8;
        $lines = padDiagramWrap ( $name, max ( 1, (int) floor ( $room / 7 ) ) );
        $wide  = max ( array_map ( 'padDiagramWidth', $lines ) );

        if ( $name === '' )
          list ( $turn, $lines, $need ) = [ FALSE, [], 32 ];
        elseif ( $wide <= $room and count ( $lines ) <= 4 )
          list ( $turn, $need ) = [ FALSE, 16 * count ( $lines ) + 14 ];
        else {
          $side  = padDiagramWrap ( $name, 18 );
          $lines = ( count ( $side ) <= max ( 1, (int) floor ( ( $w - 2 ) / 15 ) ) ) ? $side : [ $name ];
          $turn  = TRUE;
          $need  = max ( array_map ( 'padDiagramWidth', $lines ) ) + 14;
        }

        $high [$row] = max ( $high [$row], $need );
        $pieces []   = [ 'field' => $f, 'row' => $row, 'from' => $bit, 'to' => $end, 'w' => $w, 'lines' => $lines, 'turn' => $turn ];

      }

    // The rows one under the other: the numbers over a row, then its boxes.

    $top = 12 + ( $heading !== '' ? 30 : 0 );
    $at  = [];

    foreach ( $high as $row => $h ) {
      $at [$row] = $top + 14;
      $top      += 14 + $h + 4;
    }

    $width  = (int) ceil ( 24 + min ( $next, $bits ) * $bitW );
    $width  = max ( $width, $heading !== '' ? (int) ceil ( padDiagramWidth ( $heading ) * 1.2 + 24 ) : 0 );
    $height = (int) ceil ( $top + 8 );
    $X      = fn ( $v ) => padChartXY ( $v );

    $desc = [];
    foreach ( $fields as list ( $from, $to, $name ) )
      $desc [] = padDiagramSay ( ( $from == $to ? "bit $from" : "bits $from-$to" ) . ( $name !== '' ? ": $name" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'diagram', [ 'packet', $fields, $bits, $heading ], $desc, $title, $width, $height, FALSE );

    $svg .= padDiagramStyle () . padDiagramPacketStyle ();

    if ( $heading !== '' )
      $svg .= '<text class="pd-pk-heading" x="12" y="30">' . padDiagramEsc ( $heading ) . '</text>';

    foreach ( $pieces as $piece ) {

      $x   = 12 + ( $piece ['from'] % $bits ) * $bitW;
      $y   = $at [ $piece ['row'] ];
      $h   = $high [ $piece ['row'] ];
      $w   = $piece ['w'];
      $mid = $x + $w / 2;

      $svg .= '<g class="pc-mark"><title>' . padDiagramEsc ( $desc [ $piece ['field'] ] ) . '</title>'
            . '<rect class="pd-pk-field pd-pk-' . ( $piece ['field'] % 2 ? 'b' : 'a' ) . '" x="' . $X ( $x ) . '" y="' . $X ( $y ) . '" width="' . $X ( $w ) . '" height="' . $X ( $h ) . '"/>';

      if ( $piece ['turn'] ) {
        $count = count ( $piece ['lines'] );
        foreach ( $piece ['lines'] as $n => $one ) {
          $tx   = $mid + ( $n - ( $count - 1 ) / 2 ) * 15 + 4;
          $ty   = $y + $h / 2;
          $svg .= '<text x="' . $X ( $tx ) . '" y="' . $X ( $ty ) . '" text-anchor="middle" transform="rotate(-90 ' . $X ( $tx ) . ' ' . $X ( $ty ) . ')">' . padDiagramEsc ( $one ) . '</text>';
        }
      } elseif ( $piece ['lines'] )
        $svg .= padDiagramLines ( $piece ['lines'], $mid, $y + $h / 2 );

      // The numbers of the first and the last bit over the piece, as many as fit: both, the
      // first, or none - a field of one bit far down a long frame has no room for its four
      // digits.

      $one  = (string) $piece ['from'];
      $two  = (string) $piece ['to'];
      $size = fn ( $text ) => strlen ( $text ) * 5.6;

      if ( $piece ['from'] != $piece ['to'] and $size ( $one ) + $size ( $two ) + 6 <= $w )
        $svg .= '<text class="pd-pk-bit" x="' . $X ( $x + 2 ) . '" y="' . $X ( $y - 3 ) . '">' . $one . '</text>'
              . '<text class="pd-pk-bit" x="' . $X ( $x + $w - 2 ) . '" y="' . $X ( $y - 3 ) . '" text-anchor="end">' . $two . '</text>';
      elseif ( $size ( $one ) + 2 <= $w )
        $svg .= '<text class="pd-pk-bit" x="' . $X ( $mid ) . '" y="' . $X ( $y - 3 ) . '" text-anchor="middle">' . $one . '</text>';

      $svg .= '</g>';

    }

    return "$svg</svg>";

  }

  // The rules of the packet diagram: the fields by turns in the colours of a node and of a
  // round node, the numbers of the bits small. A field is a node of its own colour by two
  // classes, so the .pd-node of a diagram further down the page does not paint it over.

  function padDiagramPacketStyle () {

    return '<style' . padStyleNonce () . '>'
         . '.pad-chart-diagram .pd-pk-field.pd-pk-a{fill:var(--pad-diagram-node);stroke:var(--pad-diagram-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-pk-field.pd-pk-b{fill:var(--pad-diagram-round);stroke:var(--pad-diagram-round-border);stroke-width:1.5}'
         . '.pad-chart-diagram .pd-pk-bit{font-size:9px;font-variant-numeric:tabular-nums}'
         . '.pad-chart-diagram .pd-pk-heading{font-size:15px;font-weight:700}'
         . '</style>';

  }

?>
