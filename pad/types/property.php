<?php

  // Type handler for a tag property ({property:count}, and the {&name} variable form): returns
  // the property's value through padTagValue().
  //
  // When the tag's first parameter names an enclosing level - by name, by tag or by number -
  // the property is read from that level, which is the {property:count 'items'} form; otherwise
  // it comes from the nearest enclosing tag.

  // A name that names no enclosing level is the mistake {count@nosuch} is, and is named as
  // that one is: {property:count 'rowz'} answered the count of the innermost loop. What is
  // no name - the ternary of {even@xs ? even : odd} - is not a level.

  if ( padTagFieldSearch ( $padParm ) )
    $padPropertyValue = padTagValue ( $padParm . ':' . $padTag[$pad], 1 );

  else {

    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_-]*$/D', trim ( (string) $padParm ) ) and $GLOBALS ['padCheckSyntax'] )
      padError ( "there is no enclosing level named '$padParm' for the property '" . $padTag[$pad] . "'" );

    $padPropertyValue = padTagValue ( $padTag[$pad], 1 );

  }

  // A property that answers text is written as the field it comes from is, through the end
  // chain - sanitize by default: firstFieldValue, firstFieldName and key answer a row's data,
  // and {firstFieldValue@rows} or {key@kv} wrote a value or a key holding <script> to the
  // page live, where {$name} escaped it. Numbers and truths - count, current, first - and the
  // data sets of data@ and fields@ are no text and stay as they are; a condition reads the
  // property through the expression, never through here, so it compares the value itself.

  if ( is_string ( $padPropertyValue ) )
    foreach ( $GLOBALS ['padDataDefaultEnd'] as $padOptOne )
      $padPropertyValue = padEval ( $padOptOne, $padPropertyValue );

  return $padPropertyValue;

?>
