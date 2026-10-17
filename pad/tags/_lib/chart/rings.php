<?php

  // Several goals at once - concentric progress rings, the activity rings of a watch.
  //
  //   {chart 'rings', data='today', label='goal', value='done', to='target'}
  //   {chart 'rings', data='projects', label='name', value='percent'}
  //
  // A row is a ring, the first outermost, eight at most: an arc of value= - else the first
  // numeric field - against to=, one number for every row (100) or a field of the row,
  // running clockwise from twelve o'clock over a light track of its own colour, with
  // rounded ends. A ring past its goal is full. label= names the row in the legend beside
  // the rings, with the value, the goal and the share.

  function padChartRings ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used  = [];
    $field = padChartPick ( 'value', $numbers, $used );
    $label = padChartPick ( 'label', $names, $used );
    $to    = (string) padTagParm ( 'to', 100 );
    $rings = [];
    $desc  = [];
    $index = 0;

    if ( is_numeric ( $to ) and ( ! padChartFinite ( $to ) or $to <= 0 ) ) {
      if ( $padCheckSyntax )
        padError ( 'the goal of a rings chart is a number above 0' );
      return '';
    }

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$field] ?? NULL;
      $end = is_numeric ( $to ) ? $to : ( $row [$to] ?? NULL );

      if ( ! padChartFinite ( $v ) or $v < 0 or ! padChartFinite ( $end ) or $end <= 0 )
        continue;

      $name = padChartText ( $row, $label );
      $name = ( $name !== '' ) ? $name : "Row $index";
      $v    = $v + 0;
      $end  = $end + 0;
      $part = $v / $end;

      $rings [] = [ $name, $v, $end, $part ];
      $desc  [] = "$name: " . padChartNumber ( $v ) . ' of ' . padChartNumber ( $end ) . ', ' . padChartNumber ( round ( $part * 100 ) ) . '%';

    }

    if ( ! $rings )
      return '';

    if ( count ( $rings ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( 'the rings chart has ' . count ( $rings ) . ' rows - eight at most, each its own colour' );
      $rings = array_slice ( $rings, 0, 8 );
      $desc  = array_slice ( $desc,  0, 8 );
    }

    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ( is_numeric ( $to ) ? '' : " of $to" ) );

    list ( $id, $svg ) = padChartOpen ( 'rings', $rings, $desc, $title, $width, $height );

    // The rings on the left as large as the height lets them, the legend in the room
    // beside them: a ring as thick as the radius allows, a gap of a fifth of that between.

    $legendW = 0;
    foreach ( $rings as $i => $ring )
      $legendW = max ( $legendW, padChartWidth ( $ring [0] ), padChartWidth ( padChartNumber ( $ring [1] ) . ' of ' . padChartNumber ( $ring [2] ) . ' · 100%' ) );

    $n     = count ( $rings );
    $r     = max ( 16, min ( ( $height - 20 ) / 2, ( $width - min ( $legendW, $width / 2 ) - 64 ) / 2 ) );
    $thick = max ( 3, min ( 30, $r * 0.72 / ( $n * 1.2 ) ) );
    $gap   = $thick * 0.2;
    $cx    = 12 + $r;
    $cy    = $height / 2;

    foreach ( $rings as $i => list ( $name, $v, $end, $part ) ) {

      $radius = $r - $thick / 2 - $i * ( $thick + $gap );
      $line   = 'pc-l' . ( $i + 1 );
      $stroke = 'stroke-width:' . padChartXY ( $thick );
      $tip    = '<title>' . padChartAttr ( $desc [$i] ) . '</title>';

      $svg .= '<circle class="' . $line . '" stroke-opacity="0.18" style="' . $stroke . '" cx="' . padChartXY ( $cx ) . '" cy="' . padChartXY ( $cy ) . '" r="' . padChartXY ( $radius ) . "\">$tip</circle>";

      if ( $part >= 1 )
        $svg .= '<circle class="pc-mark ' . $line . '" style="' . $stroke . '" cx="' . padChartXY ( $cx ) . '" cy="' . padChartXY ( $cy ) . '" r="' . padChartXY ( $radius ) . "\">$tip</circle>";
      elseif ( $part > 0 ) {
        $a    = 2 * M_PI * $part;
        $svg .= '<path class="pc-mark ' . $line . '" style="' . $stroke . '" d="M' . padChartXY ( $cx ) . ',' . padChartXY ( $cy - $radius )
              . 'A' . padChartXY ( $radius ) . ',' . padChartXY ( $radius ) . ' 0 ' . ( $part > 0.5 ? 1 : 0 ) . ' 1 '
              . padChartXY ( $cx + $radius * sin ( $a ) ) . ',' . padChartXY ( $cy - $radius * cos ( $a ) ) . "\">$tip</path>";
      }

    }

    // The legend: a dot in the ring's colour, the name, and under it the value of the goal
    // and the share - the value alone, as a share, when the goal is 100 - beside the name
    // when the height leaves no room for two lines.

    $lx    = $cx + $r + 28;
    $lineH = min ( 36, ( $height - 12 ) / $n );
    $two   = ( $lineH >= 30 );
    $ly    = $cy - $n * $lineH / 2;
    $room  = $width - $lx - 8;

    foreach ( $rings as $i => list ( $name, $v, $end, $part ) ) {

      $y     = $ly + $i * $lineH + ( $two ? 0 : $lineH / 2 - 10 );
      $value = ( $end == 100 ) ? padChartNumber ( $v ) . '%' : padChartNumber ( $v ) . ' of ' . padChartNumber ( $end ) . ' · ' . padChartNumber ( round ( $part * 100 ) ) . '%';
      $svg  .= '<circle class="' . padChartSlot ( $i ) . '" cx="' . padChartXY ( $lx + 5 ) . '" cy="' . padChartXY ( $y + 10 ) . '" r="5"/>';

      if ( $two )
        $svg .= '<text x="' . padChartXY ( $lx + 16 ) . '" y="' . padChartXY ( $y + 14 ) . '" style="font-weight:600">' . padChartAttr ( padChartFit ( $name, $room - 16 ) ) . '</text>'
              . '<text x="' . padChartXY ( $lx + 16 ) . '" y="' . padChartXY ( $y + 28 ) . '">' . padChartAttr ( padChartFit ( $value, $room - 16 ) ) . '</text>';
      else
        $svg .= '<text x="' . padChartXY ( $lx + 16 ) . '" y="' . padChartXY ( $y + 14 ) . '">' . padChartAttr ( padChartFit ( "$name: $value", $room - 16 ) ) . '</text>';

    }

    return "$svg</svg>";

  }

?>
