<?php

  // {map 'world', data='sales', key='country', value='amount'} - a choropleth map as inline
  // SVG, drawn by tags/_lib/map.php from Natural Earth's country outlines. The first parameter is
  // the view: world (the default), europe, africa, asia, north-america, south-america or
  // oceania. data= names the rows as for {chart} - a {data} store, a page array or a _data
  // file - and as a pair the content is the rows, JSON, YAML, XML or CSV:
  //
  //   {map 'europe', key='country', value='visitors'}
  //     country,visitors
  //     NL,1200
  //   {/map}
  //
  // key= names the field with the country - its ISO alpha-2 or alpha-3 code or its English
  // name, in any case - else the first field that is no number; value= the number, else the
  // first numeric field. Rows of one country add up; a key no country has is named in the
  // title, not drawn. scale= is linear (the default: seven steps from the lowest value to
  // the highest) or quantile (seven steps by rank). width= is the width (720), height=
  // follows from the view unless given, title= names the map for a screen reader. Without
  // data the map is drawn in grey.

  $padMapView  = strtolower ( trim ( (string) $padParm ) );
  $padMapView  = ( $padMapView === '' ) ? 'world' : $padMapView;
  $padMapViews = array_keys ( padMapViews () );

  if ( ! in_array ( $padMapView, $padMapViews ) ) {
    if ( $padCheckSyntax )
      padError ( "the map has no view named '" . padMakeSafe ( $padMapView, 20 ) . "' - " . implode ( ', ', $padMapViews ) );
    $padMapView = 'world';
  }

  $padMapSource = $padContent;
  $padContent   = '';
  $padMapRows   = ( isset ( $padPrm [$pad] ['data'] ) or trim ( $padMapSource ) !== '' ) ? padChartRows ( $padMapSource, 'map' ) : [];
  $padData [$pad] = padDefaultData ();

  list ( $padMapNumbers, $padMapNames ) = padChartGuess ( $padMapRows );

  $padMapUsed   = [];
  $padMapKey    = padChartPick ( 'key',   $padMapNames,   $padMapUsed );
  $padMapValue  = padChartPick ( 'value', $padMapNumbers, $padMapUsed );
  $padMapValues = [];
  $padMapMissed = [];

  // A row that is a value alone - [ 'NL' => 1200, 'BE' => 800 ], the natural shape of a
  // map's data - is the value of the country its key names: the key was never read, the
  // map drew nothing and its title listed an empty name as not on the map. So is a row of
  // one field keyed by a name when no field holds the country - such an object in a {data}
  // store or as the map's content, whose values became rows of one field under the store's
  // name: { "NL": 1200 } was a row [ v => 1200 ] keyed NL, and the map said No values.

  foreach ( (array) $padMapRows as $padMapName => $padMapRow ) {

    $padMapAlone = ( is_string ( $padMapName ) and ( is_scalar ( $padMapRow )
                     or ( $padMapKey === '' and count ( padChartRow ( $padMapRow ) ) == 1 ) ) );
    $padMapRow   = padChartRow ( $padMapRow );
    $padMapName  = $padMapAlone ? $padMapName : padChartText ( $padMapRow, $padMapKey );
    $padMapCode  = padMapCode ( $padMapName );
    $padMapNum   = $padMapRow [$padMapValue] ?? NULL;

    if ( ! padChartFinite ( $padMapNum ) )
      continue;

    if ( $padMapCode === '' and trim ( $padMapName ) !== '' )
      $padMapMissed [] = $padMapName;
    elseif ( $padMapCode !== '' )
      $padMapValues [$padMapCode] [0] = ( $padMapValues [$padMapCode] [0] ?? 0 ) + $padMapNum;

  }

  $padMapTitle = (string) padTagParm ( 'title', ( $padMapValue !== '' ? ucfirst ( $padMapValue ) . ' by country' : ucfirst ( $padMapView ) ) );

  if ( $padMapMissed )
    $padMapTitle .= ' (not on the map: ' . implode ( ', ', array_slice ( array_unique ( $padMapMissed ), 0, 10 ) ) . ')';

  $padMapScale = strtolower ( trim ( (string) padTagParm ( 'scale', 'linear' ) ) );

  if ( ! in_array ( $padMapScale, [ 'linear', 'quantile' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the map has no scale '" . padMakeSafe ( $padMapScale, 20 ) . "' - linear or quantile" );
    $padMapScale = 'linear';
  }

  return padMap ( $padMapView, $padMapValues,
                  $padMapTitle,
                  max ( 100, (int) padTagParm ( 'width', 720 ) ),
                  (int) padTagParm ( 'height', 0 ),
                  $padMapScale );

?>
