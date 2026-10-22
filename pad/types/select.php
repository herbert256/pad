<?php

  // Type handler for a declared select table ({select:users}, or a bare tag naming a table in
  // $padSelect): builds and runs the query with padSelect() and returns the rows for the level
  // to iterate, joining to an enclosing select through the declared relations.
  //
  // With the htmlAttrJson option - on the tag or declared with the table - the rows come back
  // as one HTML-attribute-safe JSON string instead, for handing a table straight to a React
  // component. padSelect says so in $padHtmlAttrJson: the tag alone was asked, so a table
  // declared with it got the shape it named, iterated, and no JSON.

  include_once PAD . 'select/_lib/select.php';

  $padSelectData = padSelect ( $padTag [$pad] );

  if ( ! $padHtmlAttrJson )
    return $padSelectData;

  // The values keep the types db() gives them - integer and float columns as numbers -
  // where every numeric-looking string was made one, "007" a 7.

  // Every row, as a list in the order of the select: db() keys the rows of an array by an id
  // column when they hold one, and as JSON that was an object keyed by id - no .map() for the
  // component, and the keys read back in number order, not the order the SQL gave.

  if ( $padHtmlAttrJson == 'array' and is_array ( $padSelectData ) )
    $padSelectData = array_values ( $padSelectData );

  return padJsonForHtmlAttr ( $padSelectData ) ;

?>
