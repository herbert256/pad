<?php

  // A live change of the editable spreadsheet of spreadsheet/live that carries more than 1000
  // values - in padValue, as any visitor can post it, or as fields - comes back computed: the
  // sheet reads only the fields of its own cells, which a change submits as a form, and PHP
  // keeps the first 1000 fields of a post, the region's name the first of them. padValue was
  // read with parse_str, which warned past max_input_vars, and the request ended with a 500.

  $liveUrl   = $padHost . 'regression/pages/?spreadsheet/live&padInclude';
  $liveTotal = fn ( $html ) => preg_match ( '/data-formula="=SUM\(D2:D3\)" data-value="([^"]*)"/', $html, $match ) ? $match [1] : 'none';

  $liveMany = [];

  for ( $liveAt = 1; $liveAt <= 1001; $liveAt++ )
    $liveMany [] = "Z$liveAt=$liveAt";

  $liveValue = padCurl ( [ 'url' => $liveUrl, 'post' => [
    'padLive'            => 'shop',
    'padEvent'           => 'sheet-shop',
    'padValue'           => implode ( '&', $liveMany ),
    'padSheet[shop][C2]' => '4'
  ] ] );

  $liveFields = [ 'padLive' => 'shop', 'padSheet[shop][C2]' => '4' ];

  for ( $liveAt = 1; $liveAt <= 1100; $liveAt++ )
    $liveFields ["padSheet[shop][Z$liveAt]"] = (string) $liveAt;

  $liveFields += [ 'padEvent' => 'sheet-shop', 'padValue' => '' ];

  $liveField = padCurl ( [ 'url' => $liveUrl, 'post' => $liveFields ] );

  $liveResult = '1001 values: ' . $liveValue ['result'] . ', the region alone '
              . ( str_starts_with ( ltrim ( $liveValue ['data'] ), '<div data-pad-live' ) ? 'no' : 'yes' ) . ', total ' . $liveTotal ( $liveValue ['data'] )
              . '; 1100 fields: ' . $liveField ['result'] . ', the region alone '
              . ( str_starts_with ( ltrim ( $liveField ['data'] ), '<div data-pad-live' ) ? 'no' : 'yes' ) . ', total ' . $liveTotal ( $liveField ['data'] );

?>
