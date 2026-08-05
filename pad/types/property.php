<?php

  // Type handler for a tag property ({property:count}, and the {&name} variable form): returns
  // the property's value through padTagValue().
  //
  // When the tag's first parameter names an enclosing level - by name, by tag or by number -
  // the property is read from that level, which is the {property:count 'items'} form; otherwise
  // it comes from the nearest enclosing tag.

  if ( padTagFieldSearch ( $padParm ) )
    return padTagValue ( $padParm . ':' . $padTag[$pad], 1 );
  else
    return padTagValue ( $padTag[$pad], 1 );

?>