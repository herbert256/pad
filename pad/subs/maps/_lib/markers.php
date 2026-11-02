<?php

  // Normalize geographic point rows before they reach the SVG. Region outlines have
  // their own projection (and sometimes insets), so they cannot use the world transform.
  function padMapMarkers ( $rows, $view, $latitude = 'latitude', $longitude = 'longitude', $label = 'label', $value = '' ) {

    global $padCheckSyntax;
    if ( ! is_array ( $rows ) || count ( $rows ) > 500 ) {
      if ( $padCheckSyntax ) padError ( 'a marker map takes at most 500 point rows' );
      return [];
    }
    if ( ! $rows ) return [];
    if ( isset ( padMapView ( $view ) ['outlines'] ) ) {
      if ( $padCheckSyntax ) padError ( 'geographic markers require a world or regional country map; state and province maps use a different projection' );
      return [];
    }
    foreach ( [ $latitude, $longitude, $label, $value ] as $field )
      if ( ! is_string ( $field ) ) {
        if ( $padCheckSyntax ) padError ( 'map marker field names must be text' );
        return [];
      }
    $markers = [];
    foreach ( $rows as $row ) {
      $lat = is_array ( $row ) ? ( $row [$latitude] ?? NULL ) : NULL;
      $lon = is_array ( $row ) ? ( $row [$longitude] ?? NULL ) : NULL;
      if ( ! padChartFinite ( $lat ) || ! padChartFinite ( $lon ) || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180 ) {
        if ( $padCheckSyntax ) padError ( 'map markers need finite latitude (-90 to 90) and longitude (-180 to 180)' );
        continue;
      }
      $amount = $value === '' ? NULL : ( $row [$value] ?? NULL );
      if ( $value !== '' && ( ! padChartFinite ( $amount ) || $amount < 0 ) ) {
        if ( $padCheckSyntax ) padError ( 'map bubble values must be finite nonnegative numbers' );
        continue;
      }
      // Zero has no area; do not give it an arbitrary visible minimum radius.
      if ( $value !== '' && (float) $amount === 0.0 ) continue;
      $name = $row [$label] ?? '';
      $name = is_scalar ( $name ) ? trim ( padUnprotect ( (string) $name ) ) : '';
      $markers [] = [ 'latitude' => (float) $lat, 'longitude' => (float) $lon,
        'label' => $name ?: padChartNumber ( $lat ) . ', ' . padChartNumber ( $lon ),
        'value' => $amount === NULL ? NULL : (float) $amount ];
    }
    return $markers;

  }

  function padMapMarkerView ( $markers, $view ) {

    [ $west, $south, $east, $north ] = padMapView ( $view ) ['box'];
    [ $x0, $y0, $x1, $y1 ] = padMapBox ( $view );
    $inside = []; $outside = 0;
    foreach ( $markers as $marker ) {
      [ $x, $y ] = padMapProject ( $marker ['longitude'], $marker ['latitude'] );
      if ( $marker ['longitude'] < $west || $marker ['longitude'] > $east || $marker ['latitude'] < $south || $marker ['latitude'] > $north
           || $x < $x0 || $x > $x1 || $y < $y0 || $y > $y1 ) { $outside++; continue; }
      $inside [] = $marker + [ 'x' => $x, 'y' => $y ];
    }
    return [ $inside, $outside ];

  }

  function padMapMarkerSvg ( $markers, $unitsPerPixel ) {

    $maximum = max ( array_column ( $markers, 'value' ) ?: [ 0 ] );
    // Large bubbles first so a smaller neighbouring point remains visible above one.
    usort ( $markers, fn ( $a, $b ) => ( $b ['value'] ?? 0 ) <=> ( $a ['value'] ?? 0 ) );
    $svg = '<g class="pm-markers">';
    foreach ( $markers as $marker ) {
      $radius = $marker ['value'] === NULL ? 5 : 18 * sqrt ( $marker ['value'] / $maximum );
      $name = $marker ['label'] . ( $marker ['value'] === NULL ? '' : ': ' . padChartNumber ( $marker ['value'] ) );
      $svg .= '<circle class="pc-dot pm-marker" cx="' . padChartXY ( $marker ['x'] ) . '" cy="' . padChartXY ( $marker ['y'] )
        . '" r="' . padChartXY ( $radius * $unitsPerPixel ) . '" vector-effect="non-scaling-stroke"><title>' . padChartAttr ( $name ) . '</title></circle>';
    }
    return $svg . '</g>';

  }

?>
