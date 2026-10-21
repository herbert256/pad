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
    return padTagValue ( $padParm . ':' . $padTag[$pad], 1 );

  if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_-]*$/D', trim ( (string) $padParm ) ) and $GLOBALS ['padCheckSyntax'] )
    padError ( "there is no enclosing level named '$padParm' for the property '" . $padTag[$pad] . "'" );

  return padTagValue ( $padTag[$pad], 1 );

?>
