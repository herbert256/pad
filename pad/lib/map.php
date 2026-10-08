<?php

  // Choropleth maps - the {map} tag. Every country of the world as inline SVG, coloured by
  // a value of its row: no JavaScript, no tiles, no remote service, so a map works in print
  // and in an e-mail and caches like any other output.
  //
  //   {map 'world', data='sales', key='country', value='amount'}
  //   {map 'europe', data='visitors', title='Visitors per country'}
  //
  // The outlines are lib/map/world.json: Natural Earth's 1:50m admin-0 countries (public
  // domain, naturalearthdata.com) as world-atlas@2.0.2 publishes them (ISC licence), turned
  // from TopoJSON into SVG paths in the Natural Earth projection, each under its ISO 3166
  // alpha-2 code with its alpha-3 code and English names (codes and names from
  // i18n-iso-countries, MIT). A path is kept twice: at full detail for a region, and
  // simplified for the whole world, where the detail is smaller than a pixel.
  //
  // padMapCountries   the countries, read once per request, and an index of every code and
  //                   name in lower case
  // padMapViews       the views: the world and the regions, as boxes of longitude and
  //                   latitude
  // padMap            the SVG
  //
  // The colour is the heatmap's: seven steps of one hue, from --pad-chart-heat-0 to
  // --pad-chart-heat-6, with a scale under the map; a country without a value is
  // --pad-map-empty, the borders --pad-map-border - custom properties with light-dark()
  // defaults on .pad-chart-map.

  function padMapCountries () {

    static $countries = NULL, $index = NULL;

    if ( $countries === NULL ) {

      $countries = json_decode ( file_get_contents ( PAD . 'lib/map/world.json' ), TRUE ) ['countries'];
      $index     = [];

      foreach ( $countries as $code => list ( $alpha3, $name, $others ) )
        foreach ( array_merge ( [ $code, $alpha3, $name ], $others ) as $key )
          $index [ mb_strtolower ( $key ) ] ??= $code;

    }

    return [ $countries, $index ];

  }

  // The country a key names - ISO alpha-2, alpha-3 or an English name, in any case - as its
  // alpha-2 code, else ''.

  function padMapCode ( $key ) {

    list ( , $index ) = padMapCountries ();

    return $index [ mb_strtolower ( trim ( (string) $key ) ) ] ?? '';

  }

  // The views as [ west, south, east, north ] in degrees.

  function padMapViews () {

    return [ 'world'         => [ -180, -58,  180, 84 ],
             'europe'        => [ -25,   34,   42, 71.5 ],
             'africa'        => [ -19,  -36,   53, 38 ],
             'asia'          => [  25,   -11, 150, 56 ],
             'north-america' => [ -170,   7,  -50, 84 ],
             'south-america' => [ -83,  -56,  -33, 14 ],
             'oceania'       => [ 110,  -48,  180, 0 ] ];

  }

  // A point in the units of world.json: the Natural Earth projection, as the file was made.

  function padMapProject ( $lon, $lat ) {

    $l  = deg2rad ( $lon );
    $p  = deg2rad ( $lat );
    $p2 = $p * $p;
    $p4 = $p2 * $p2;
    $x  = $l * ( 0.8707 - 0.131979 * $p2 + $p4 * ( -0.013791 + $p4 * ( 0.003971 * $p2 - 0.001529 * $p4 ) ) );
    $y  = $p * ( 1.007226 + $p2 * ( 0.015085 + $p4 * ( -0.044475 + 0.028874 * $p2 - 0.005916 * $p4 ) ) );

    return [ ( $x + 2.7354 ) * 1000, ( 1.4224 - $y ) * 1000 ];

  }

  // The box a view takes in the projection: its edges walked, since a meridian bends.

  function padMapBox ( $view ) {

    list ( $west, $south, $east, $north ) = padMapViews () [$view];

    $xs = $ys = [];

    for ( $k = 0; $k <= 20; $k++ ) {
      $lon = $west  + ( $east  - $west  ) * $k / 20;
      $lat = $south + ( $north - $south ) * $k / 20;
      foreach ( [ [ $lon, $south ], [ $lon, $north ], [ $west, $lat ], [ $east, $lat ] ] as list ( $a, $b ) ) {
        list ( $xs [], $ys [] ) = padMapProject ( $a, $b );
      }
    }

    return [ min ( $xs ), min ( $ys ), max ( $xs ), max ( $ys ) ];

  }

  // The SVG. $values is code => [ value ]; $width the width of the picture, the height
  // follows from the view unless $height is given; $scale is linear - the steps evenly
  // over the values - or quantile - evenly over their ranks. A country whose box lies
  // outside the view is not written at all.

  function padMap ( $view, $values, $title, $width, $height, $scale = 'linear' ) {

    list ( $countries ) = padMapCountries ();
    list ( $x0, $y0, $x1, $y1 ) = padMapBox ( $view );

    $boxW   = $x1 - $x0;
    $boxH   = $y1 - $y0;
    $legend = $values ? 34 : 0;

    if ( $height <= 0 )
      $height = (int) round ( $width * $boxH / $boxW ) + $legend;

    $mapH   = max ( 10, $height - $legend );
    $simple = ( $boxW / $width > 3 );
    $desc   = [];

    foreach ( $values as $code => list ( $value ) )
      $desc [] = $countries [$code] [1] . ': ' . padChartNumber ( $value );

    if ( ! $desc )
      $desc [] = 'No values';

    list ( $id, $svg ) = padChartOpen ( 'map', [ $view, $values ], $desc, $title, $width, $height );

    $svg .= padMapStyle ();

    $all  = array_column ( $values, 0 );
    $min  = $all ? min ( $all ) : 0;
    $max  = $all ? max ( $all ) : 0;
    $span = $max - $min;
    $step = fn ( $v ) => ( is_finite ( $span ) and $span > 0 ) ? (int) round ( ( $v - $min ) / $span * 6 ) : 6;

    // By rank instead: the values in order, spread evenly over the steps - one large value
    // no longer leaves every other country in the lightest step.

    if ( $scale == 'quantile' and count ( $all ) > 1 ) {
      $ranks = array_values ( array_unique ( $all, SORT_REGULAR ) );
      sort ( $ranks );
      $last  = max ( 1, count ( $ranks ) - 1 );
      $step  = fn ( $v ) => (int) round ( array_search ( $v, $ranks ) / $last * 6 );
    }

    $svg .= '<svg x="0" y="0" width="' . $width . '" height="' . $mapH . '" viewBox="' . padChartXY ( $x0 ) . ' ' . padChartXY ( $y0 ) . ' ' . padChartXY ( $boxW ) . ' ' . padChartXY ( $boxH )
          . '" preserveAspectRatio="xMidYMid meet">';

    // The countries without a value first, so the borders of the coloured ones lie on top.

    $later = '';

    foreach ( $countries as $code => $country ) {

      list ( , $name, , $box, $detail, $plain ) = $country;

      if ( $box [2] < $x0 or $box [0] > $x1 or $box [3] < $y0 or $box [1] > $y1 )
        continue;

      $d = $simple ? $plain : $detail;

      if ( isset ( $values [$code] ) )
        $later .= '<path class="pc-mark pm-land pc-h' . $step ( $values [$code] [0] ) . "\" d=\"$d\"><title>" . padChartAttr ( $name . ': ' . padChartNumber ( $values [$code] [0] ) ) . '</title></path>';
      else
        $svg .= "<path class=\"pm-land pm-empty\" d=\"$d\"><title>" . padChartAttr ( $name ) . '</title></path>';

    }

    $svg .= "$later</svg>";

    if ( ! $values )
      return "$svg</svg>";

    // The scale: the lowest value, the seven steps, the highest, and the grey of no value.

    $low  = padChartNumber ( $min );
    $high = padChartNumber ( $max );
    $x    = 10 + padChartWidth ( $low ) + 6;
    $y    = $height - 20;

    $svg .= '<text x="10" y="' . ( $y + 9 ) . '">' . $low . '</text>';

    for ( $k = 0; $k <= 6; $k++ )
      $svg .= '<rect class="pc-h' . $k . '" x="' . padChartXY ( $x + $k * 18 ) . "\" y=\"$y\" width=\"18\" height=\"10\"/>";

    $x    += 7 * 18 + 6;
    $svg  .= '<text x="' . padChartXY ( $x ) . '" y="' . ( $y + 9 ) . '">' . $high . '</text>';
    $x    += padChartWidth ( $high ) + 18;
    $svg  .= '<rect class="pm-empty" x="' . padChartXY ( $x ) . "\" y=\"$y\" width=\"18\" height=\"10\"/>"
           . '<text x="' . padChartXY ( $x + 24 ) . '" y="' . ( $y + 9 ) . '">no value</text>';

    return "$svg</svg>";

  }

  // The rules of the map: the land, its borders and the grey of no value. The borders keep
  // their width whatever the map is scaled to.

  function padMapStyle () {

    $roles = [ 'empty' => [ '#e4e3df', '#3a3a37' ], 'border' => [ '#fcfcfb', '#1a1a19' ] ];

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-map-$role:$day;";
      $both  .= "--pad-map-$role:light-dark($day,$night);";
    }

    return '<style>'
         . ":where(.pad-chart-map){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart-map){{$both}}}"
         . '.pad-chart-map .pm-land{stroke:var(--pad-map-border);stroke-width:.6px;vector-effect:non-scaling-stroke;stroke-linejoin:round}'
         . '.pad-chart-map .pm-empty{fill:var(--pad-map-empty)}'
         . '.pad-chart-map .pm-land:hover{opacity:.8}'
         . '</style>';

  }

?>
