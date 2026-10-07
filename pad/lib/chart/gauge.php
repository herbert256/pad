<?php

  // One number against its range: a half ring filled up to the value.
  //
  //   {chart 'gauge', value=72, label='Disk in use', unit='%'}
  //   {chart 'gauge', data='server', value='cpu', from=0, to=100, bands='60, 85', target=75}
  //
  // value= is the number itself, or the field of the first row that holds it - else the
  // first numeric field. from= and to= are the ends of the scale (0 and 100); a value past
  // an end fills the ring to that end. bands= lists up to two thresholds that colour the
  // track - green, amber, red from low to high - and target= marks a value on the ring.
  // label= names the number under it: a field of the row when it has one of that name,
  // else the text itself; unit= follows the number.

  function padChartGauge ( $rows, $width, $height ) {

    global $padCheckSyntax;

    $value = padTagParm ( 'value' );
    $row   = [];

    foreach ( (array) $rows as $first ) {
      $row = padChartRow ( $first );
      break;
    }

    if ( ! is_numeric ( $value ) ) {
      list ( $numbers ) = padChartGuess ( $rows );
      $field = ( (string) $value !== '' ) ? (string) $value : ( $numbers [0] ?? '' );
      $value = $row [$field] ?? NULL;
    }

    if ( ! padChartFinite ( $value ) )
      return '';

    $value = $value + 0;
    $from  = padTagParm ( 'from', 0 );
    $to    = padTagParm ( 'to', 100 );

    if ( ! padChartFinite ( $from ) or ! padChartFinite ( $to ) or $to <= $from ) {
      if ( $padCheckSyntax )
        padError ( 'the gauge needs from= below to=' );
      return '';
    }

    $from   = $from + 0;
    $to     = $to + 0;
    $label  = (string) padTagParm ( 'label' );
    $label  = isset ( $row [$label] ) && is_scalar ( $row [$label] ) ? (string) $row [$label] : $label;
    $unit   = (string) padTagParm ( 'unit' );
    $target = padTagParm ( 'target' );
    $bands  = array_values ( array_filter ( array_map ( 'trim', explode ( ',', (string) padTagParm ( 'bands' ) ) ), 'is_numeric' ) );
    $shown  = padChartNumber ( $value ) . $unit;
    $title  = (string) padTagParm ( 'title', $label !== '' ? $label : 'Gauge' );

    $desc = [ ( $label !== '' ? "$label: " : '' ) . $shown . ' on a scale of ' . padChartNumber ( $from ) . ' to ' . padChartNumber ( $to ) . $unit ];

    if ( padChartFinite ( $target ) )
      $desc [] = 'target ' . padChartNumber ( $target + 0 ) . $unit;

    list ( $id, $svg ) = padChartOpen ( 'gauge', [ $value, $from, $to, $bands, $target, $label ], $desc, $title, $width, $height );

    $r     = max ( 20, min ( $width / 2 - 16, $height - 52 ) );
    $inner = $r * 0.7;
    $cx    = $width / 2;
    $cy    = 12 + $r;
    $at    = fn ( $v ) => -M_PI / 2 + ( min ( $to, max ( $from, $v ) ) - $from ) / ( $to - $from ) * M_PI;

    // The track, in bands when they are given, else one piece.

    if ( $bands ) {
      $edges = array_merge ( [ $from ], array_slice ( array_map ( 'floatval', $bands ), 0, 2 ), [ $to ] );
      $kinds = ( count ( $edges ) == 3 ) ? [ 'pc-c3', 'pc-c8' ] : [ 'pc-c3', 'pc-c4', 'pc-c8' ];
      foreach ( $kinds as $i => $kind )
        $svg .= '<path class="' . $kind . ' pc-band" d="' . padChartArc ( $cx, $cy, $inner, $r, $at ( $edges [$i] ), $at ( $edges [$i + 1] ) ) . '"/>';
    } else
      $svg .= '<path class="pc-track" d="' . padChartArc ( $cx, $cy, $inner, $r, -M_PI / 2, M_PI / 2 ) . '"/>';

    // The value: a ring of its own just inside the track, so the bands stay readable.

    if ( $at ( $value ) > -M_PI / 2 )
      $svg .= '<path class="pc-c1 pc-mark" d="' . padChartArc ( $cx, $cy, $inner + ( $bands ? ( $r - $inner ) * 0.3 : 0 ), $bands ? $r - ( $r - $inner ) * 0.3 : $r, -M_PI / 2, $at ( $value ) ) . '">'
            . '<title>' . padChartAttr ( $desc [0] ) . '</title></path>';

    if ( padChartFinite ( $target ) ) {
      $a    = $at ( $target + 0 );
      $svg .= '<line class="pc-target" x1="' . padChartXY ( $cx + ( $inner - 6 ) * sin ( $a ) ) . '" y1="' . padChartXY ( $cy - ( $inner - 6 ) * cos ( $a ) ) . '"'
            . ' x2="' . padChartXY ( $cx + ( $r + 6 ) * sin ( $a ) ) . '" y2="' . padChartXY ( $cy - ( $r + 6 ) * cos ( $a ) ) . '">'
            . '<title>' . padChartAttr ( 'Target: ' . padChartNumber ( $target + 0 ) . $unit ) . '</title></line>';
    }

    $big  = max ( 14, min ( 40, $inner * 0.45 ) );
    $svg .= '<text x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy - 2 ) . '" text-anchor="middle" style="font-size:' . padChartXY ( $big ) . 'px;font-weight:600">' . padChartAttr ( $shown ) . '</text>';

    if ( $label !== '' )
      $svg .= '<text x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy + 18 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $label, $width - 20 ) ) . '</text>';

    $svg .= '<text x="' . padChartXY ( $cx - ( $r + $inner ) / 2 ) . '" y="' . padChartXY ( $cy + 16 ) . '" text-anchor="middle">' . padChartNumber ( $from ) . '</text>'
          . '<text x="' . padChartXY ( $cx + ( $r + $inner ) / 2 ) . '" y="' . padChartXY ( $cy + 16 ) . '" text-anchor="middle">' . padChartNumber ( $to ) . '</text>';

    return "$svg</svg>";

  }

?>
