<?php

  // Type handler for a declared select table ({select:users}, or a bare tag naming a table in
  // $padSelect): builds and runs the query with padSelect() and returns the rows for the level
  // to iterate, joining to an enclosing select through the declared relations.
  //
  // With the htmlAttrJson option the rows come back as one HTML-attribute-safe JSON string
  // instead, for handing a table straight to a React component.

  if ( ! padTagParm ( 'htmlAttrJson' ) )
    return padSelect ( $padTag [$pad] );

  // The values keep the types db() gives them - integer and float columns as numbers -
  // where every numeric-looking string was made one, "007" a 7.

  $padSelectData = padSelect ( $padTag [$pad] );

  return padJsonForHtmlAttr ( $padSelectData ) ;

?>