<?php

  // Choropleth maps - the map subsystem behind the {map:auto} tag. Every country of the world as
  // inline SVG, coloured by a value of its row: no JavaScript, no tiles, no remote service,
  // so a map works in print and in an e-mail and caches like any other output.
  //
  //   {map:auto 'world', data='sales', key='country', value='amount'}
  //   {map:auto 'europe', data='visitors', title='Visitors per country'}
  //
  // The outlines are subs/maps/_lib/world.json: Natural Earth's 1:50m admin-0 countries (public
  // domain, naturalearthdata.com) as world-atlas@2.0.2 publishes them (ISC licence), turned
  // from TopoJSON into SVG paths in the Natural Earth projection, each under its ISO 3166
  // alpha-2 code with its alpha-3 code and English names (codes and names from
  // i18n-iso-countries, MIT). A path is kept twice: at full detail for a region, and
  // simplified for the whole world, where the detail is smaller than a pixel.
  //
  // A view is a file of subs/maps/types/ - the files there are the views there are (padMapViews)
  // - answering its box of longitude and latitude, and for a region that is a list of
  // countries rather than a stretch of the globe those countries, with the name its title
  // takes. Every country with a part in the box is drawn - the polygons of it that lie
  // there; with a list, the others are drawn as the quiet land around it, --pad-map-context,
  // and a value of theirs is named in the title as one the view does not show.
  //
  // A view of the regions of one country - its states or provinces - names an outlines file of
  // its own beside world.json instead of a box: 'outlines' => 'us-states' is
  // subs/maps/_lib/us-states.json, the regions under their ISO 3166-2 codes, already in the
  // projection that suits the country and in units of their own, with the box they fill and
  // the land around them; a row is keyed by the region's code, with or without the country
  // (US-CA or CA), or its name, and 'unit' says what a region is called in the title.
  //
  // padMapCountries   the countries - or a view's regions and the land around them - read
  //                   once per request, and an index of every code and name in lower case
  // padMapViews       the names of the views, padMapView the one view a name has
  // padMap            the SVG
  //
  // The colour is the heatmap's: seven steps of one hue, from --pad-chart-heat-0 to
  // --pad-chart-heat-6, with a scale under the map; a country without a value is
  // --pad-map-empty, the borders --pad-map-border - custom properties with light-dark()
  // defaults on .pad-chart-map.

  // The rows, the numbers and the SVG opening are {chart:bar}'s, subs/charts/_lib/chart.php.

  include_once PAD . 'subs/charts/_lib/chart.php';
  include_once PAD . 'subs/maps/_lib/markers.php';

  // The outlines a view draws: [ every outline by code, the index of the codes and names, the
  // codes of the regions a view of regions is about (NULL for the countries), the box of a
  // view of regions in the units of its file (NULL for the countries, whose views give
  // theirs in degrees) ]. The land around a country's regions is in the outlines, under a
  // code starting with ~, and in no index: a row is never its value.

  function padMapCountries ( $view = 'world' ) {

    static $sets = [];

    $file = padMapView ( $view ) ['outlines'] ?? 'world';

    if ( ! isset ( $sets [$file] ) ) {

      $json      = json_decode ( file_get_contents ( PAD . "subs/maps/_lib/$file.json" ), TRUE );
      $countries = $json ['countries'] ?? $json ['regions'];
      $index     = [];

      // The codes and the own names first, the aliases after them: an alias never takes a
      // name that is another country's own - DR Congo listed Congo, the Congo's name, and
      // being read first it took it.

      foreach ( $countries as $code => list ( $alpha3, $name ) )
        foreach ( [ $code, $alpha3, $name ] as $key )
          $index [ mb_strtolower ( $key ) ] ??= $code;

      foreach ( $countries as $code => list ( , , $others ) )
        foreach ( $others as $key )
          $index [ mb_strtolower ( $key ) ] ??= $code;

      $sets [$file] = [ $countries + ( $json ['context'] ?? [] ), $index,
                        isset ( $json ['regions'] ) ? array_keys ( $countries ) : NULL, $json ['_box'] ?? NULL, $json ['_insets'] ?? [] ];

    }

    return $sets [$file];

  }

  // The country a key names - ISO alpha-2, alpha-3 or an English name, in any case - as its
  // alpha-2 code, else ''; on a view of regions the region it names - its ISO 3166-2 code
  // with or without the country, or a name of it - as its full ISO 3166-2 code.

  // A key is looked up as the characters it holds: a field inside a data= literal came with
  // the stand-ins of a value - "Korea, Republic of" with another comma - and was on no map.

  function padMapCode ( $key, $view = 'world' ) {

    list ( , $index ) = padMapCountries ( $view );

    return $index [ mb_strtolower ( trim ( padUnprotect ( (string) $key ) ) ) ] ?? '';

  }

  // The views: the names of the files of subs/maps/types/ (padKinds, lib/subsystem.php) - a view
  // is added by adding its file.

  function padMapViews () {

    return padKinds ( 'maps' );

  }

  // A view as its file answers it: 'box', [ west, south, east, north ] in degrees, and
  // perhaps 'countries', the alpha-2 codes of the countries it is about, and 'name', what
  // its title says - else the name of the file; or for the regions of a country 'outlines',
  // the file of _lib/ that has them, its name and 'unit', what one region is - state,
  // province. Read once per request.

  function padMapView ( $view ) {

    static $views = [];

    if ( ! isset ( $views [$view] ) ) {
      $one             = padKind ( 'maps', $view ) ? include PAD . "subs/maps/types/$view.php" : [];
      $one ['box']   ??= [ -180, -58, 180, 84 ];
      $one ['name']  ??= ucfirst ( $view );
      $one ['unit']  ??= 'country';
      $views [$view]   = $one;
    }

    return $views [$view];

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

  // The box a view takes in the projection: its edges walked, since a meridian bends. A view
  // of regions has its box in its outlines file, in the units of its own projection.

  function padMapBox ( $view ) {

    if ( $box = padMapCountries ( $view ) [3] )
      return $box;

    list ( $west, $south, $east, $north ) = padMapView ( $view ) ['box'];

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
  // over the values - or quantile - evenly over their ranks. A country none of whose parts
  // lies in the view is not written at all (padMapShown); one that is not of the view's
  // countries, when it lists them, is the land around them, drawn without a value. A view of
  // regions is about every region of its file, and the land around them is the rest of it.

  function padMap ( $view, $values, $title, $width, $height, $scale = 'linear', $markers = [], $palette = '', $scheme = 'light' ) {

    if ( $palette !== '' ) {
      include_once PAD . 'subs/themes/palettes/_lib/palette.php';
      include_once PAD . 'subs/charts/_lib/palette.php';
      if ( ! is_string ( $palette ) || ! is_string ( $scheme ) || $markers )
        throw new InvalidArgumentException ( 'Map palettes require a choropleth, a palette name and a light/dark scheme' );
      $palette = strtolower ( trim ( $palette ) );
      // Existing linear/quantile maps encode ordered magnitude, without a diverging midpoint.
      $issues = padPaletteUsage ( $palette, 'sequential', 0, $scheme );
      if ( $issues ) throw new InvalidArgumentException ( 'Map palette: ' . implode ( '; ', $issues ) );
    }

    list ( $countries, , $regions ) = padMapCountries ( $view );
    list ( $x0, $y0, $x1, $y1 ) = padMapBox ( $view );

    $focus  = ( $regions !== NULL ) ? array_flip ( $regions )
            : ( isset ( padMapView ( $view ) ['countries'] ) ? array_flip ( padMapView ( $view ) ['countries'] ) : NULL );
    $around = ( $focus !== NULL and count ( $countries ) > count ( $focus ) );
    $boxW   = $x1 - $x0;
    $boxH   = $y1 - $y0;
    $simple = ( $boxW / $width > 3 );
    $shown  = padMapShown ( $countries, $simple, $x0, $y0, $x1, $y1 );

    // A country with a value that the view does not show is named in the title, as a key
    // that is no country is: its value was dropped from the picture without a word. Its
    // value is in nothing else - not the scale, the legend or the description, which are
    // the values the map draws: a region view is fed a wider data set, and on a Benelux map
    // with Germany at 1000 in it the Netherlands, Belgium and Luxembourg at 10 to 30 all
    // took the lightest step, the legend ran to 1,000 and the description read Germany out.

    $outside = $inside = [];

    foreach ( $values as $code => $value )
      if ( ! isset ( $shown [$code] ) or ( $focus !== NULL and ! isset ( $focus [$code] ) ) )
        $outside [] = $countries [$code] [1];
      else
        $inside [$code] = $value;

    $legend = $inside ? 34 : 0;

    if ( $height <= 0 )
      $height = (int) round ( $width * $boxH / $boxW ) + $legend;

    $mapH   = max ( 10, $height - $legend );
    $desc   = [];

    foreach ( $inside as $code => list ( $value ) )
      $desc [] = $countries [$code] [1] . ': ' . padChartNumber ( $value );

    [ $markers, $markersOutside ] = padMapMarkerView ( $markers, $view );
    foreach ( $markers as $marker )
      $desc [] = $marker ['label'] . ( $marker ['value'] === NULL ? '' : ': ' . padChartNumber ( $marker ['value'] ) );
    if ( $markersOutside )
      $title .= ' (' . $markersOutside . ' markers outside this view)';

    if ( ! $desc )
      $desc [] = 'No values';

    $insets = padMapCountries ( $view ) [4];
    if ( $insets )
      $desc [] = 'Overseas regions are shown in separate insets at different scales';

    if ( $outside )
      $title .= ' (not in this view: ' . implode ( ', ', array_slice ( $outside, 0, 10 ) ) . ')';

    list ( $id, $svg ) = padChartOpen ( 'map', $markers ? [ $view, $inside, $markers ] : [ $view, $inside ], $desc, $title, $width, $height );

    $svg .= padMapStyle ( $around );

    $all  = array_column ( $inside, 0 );
    $min  = $all ? min ( $all ) : 0;
    $max  = $all ? max ( $all ) : 0;
    $span = $max - $min;
    $step = fn ( $v ) => ( is_finite ( $span ) and $span > 0 ) ? (int) round ( ( $v - $min ) / $span * 6 ) : 6;

    // By rank instead: the values in order, spread evenly over the steps - one large value
    // no longer leaves every other country in the lightest step.

    // Values all alike have one rank: they take the step the linear scale gives them, where
    // the quantile scale put them in the lightest and the linear one in the darkest.

    if ( $scale == 'quantile' and count ( array_unique ( $all, SORT_REGULAR ) ) > 1 ) {
      $ranks = array_values ( array_unique ( $all, SORT_REGULAR ) );
      sort ( $ranks );
      $last  = max ( 1, count ( $ranks ) - 1 );
      $step  = fn ( $v ) => (int) round ( array_search ( $v, $ranks ) / $last * 6 );
    }

    $svg .= '<svg x="0" y="0" width="' . $width . '" height="' . $mapH . '" viewBox="' . padChartXY ( $x0 ) . ' ' . padChartXY ( $y0 ) . ' ' . padChartXY ( $boxW ) . ' ' . padChartXY ( $boxH )
          . '" preserveAspectRatio="xMidYMid meet">';

    // Source-authored inset frames carry their own scale warning in the description.
    foreach ( $insets as [ $ix, $iy, $iw, $ih, $label ] )
      $svg .= '<rect class="pc-grid" fill="none" x="' . padChartXY ( $ix ) . '" y="' . padChartXY ( $iy ) . '" width="' . padChartXY ( $iw ) . '" height="' . padChartXY ( $ih ) . '"/>'
            . '<text x="' . padChartXY ( $ix + $iw / 2 ) . '" y="' . padChartXY ( $iy + 24 ) . '" text-anchor="middle" font-size="20">' . padChartAttr ( $label ) . '</text>';

    // The countries without a value first, so the borders of the coloured ones lie on top.

    $later = '';

    foreach ( $shown as $code => $d ) {

      $name = $countries [$code] [1];

      if ( $focus !== NULL and ! isset ( $focus [$code] ) )
        $svg .= "<path class=\"pm-land pm-context\" d=\"$d\"><title>" . padChartAttr ( $name ) . '</title></path>';
      elseif ( isset ( $inside [$code] ) )
        $later .= '<path class="pc-mark pm-land pc-h' . $step ( $inside [$code] [0] ) . "\" d=\"$d\"><title>" . padChartAttr ( $name . ': ' . padChartNumber ( $inside [$code] [0] ) ) . '</title></path>';
      else
        $svg .= "<path class=\"pm-land pm-empty\" d=\"$d\"><title>" . padChartAttr ( $name ) . '</title></path>';

    }

    $svg .= $later;
    if ( $markers )
      $svg .= padMapMarkerSvg ( $markers, max ( $boxW / $width, $boxH / $mapH ) );
    $svg .= '</svg>';

    if ( ! $inside )
      return $palette === '' ? "$svg</svg>" : padChartPaletteApply ( "$svg</svg>", $palette, [ 'usage' => 'sequential', 'scheme' => $scheme ] );

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

    return $palette === '' ? "$svg</svg>" : padChartPaletteApply ( "$svg</svg>", $palette, [ 'usage' => 'sequential', 'scheme' => $scheme ] );

  }

  // The countries a view shows, each as the outline it is drawn with: the parts of it that
  // lie in the view, by code in the order of world.json. A country is its polygons - an
  // M and its l steps each - and one whose box lies across the edge of the view keeps those
  // whose own box touches it: by the box of the whole, Russia and the United States, whose
  // islands lie on both sides of the 180th meridian, touched every view, and a map of the
  // Benelux was nine tenths their outlines, outside the picture; France was written with
  // Guiana and Reunion on it.

  function padMapShown ( $countries, $simple, $x0, $y0, $x1, $y1 ) {

    $shown = [];

    foreach ( $countries as $code => list ( , , , $box, $detail, $plain ) ) {

      if ( $box [2] < $x0 or $box [0] > $x1 or $box [3] < $y0 or $box [1] > $y1 )
        continue;

      $d = $simple ? $plain : $detail;

      if ( $box [0] < $x0 or $box [2] > $x1 or $box [1] < $y0 or $box [3] > $y1 ) {

        $keep = '';

        foreach ( preg_split ( '/(?=M)/', $d, -1, PREG_SPLIT_NO_EMPTY ) as $part ) {
          preg_match_all ( '/-?\d+(?:\.\d+)?/', $part, $numbers );
          $numbers = $numbers [0];
          $x = $left = $right = (float) $numbers [0];
          $y = $top  = $low   = (float) ( $numbers [1] ?? 0 );
          for ( $i = 2; $i + 1 < count ( $numbers ); $i += 2 ) {
            $x    += $numbers [$i];
            $y    += $numbers [$i + 1];
            $left  = min ( $left, $x );
            $right = max ( $right, $x );
            $top   = min ( $top, $y );
            $low   = max ( $low, $y );
          }
          if ( ! ( $right < $x0 or $left > $x1 or $low < $y0 or $top > $y1 ) )
            $keep .= $part;
        }

        $d = $keep;

      }

      if ( $d !== '' )
        $shown [$code] = $d;

    }

    return $shown;

  }

  // The rules of the map: the land, its borders and the grey of no value. The borders keep
  // their width whatever the map is scaled to. A view of a list of countries draws the land
  // around them too, a shade quieter than the grey of no value - its rule only written then,
  // so the maps of the other views stay as they were.

  function padMapStyle ( $context = FALSE ) {

    $roles = [ 'empty' => [ '#e4e3df', '#3a3a37' ], 'border' => [ '#fcfcfb', '#1a1a19' ] ]
           + ( $context ? [ 'context' => [ '#f0efeb', '#292927' ] ] : [] );

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-map-$role:$day;";
      $both  .= "--pad-map-$role:light-dark($day,$night);";
    }

    return '<style' . padStyleNonce () . '>'
         . ":where(.pad-chart-map){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-chart-map){{$both}}}"
         . '.pad-chart-map .pm-land{stroke:var(--pad-map-border);stroke-width:.6px;vector-effect:non-scaling-stroke;stroke-linejoin:round}'
         . '.pad-chart-map .pm-empty{fill:var(--pad-map-empty)}'
         . ( $context ? '.pad-chart-map .pm-context{fill:var(--pad-map-context)}' : '' )
         . '.pad-chart-map .pm-land:hover{opacity:.8}'
         . '</style>';

  }

?>
