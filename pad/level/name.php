<?php

  // Fixes the level's name once and for all - the label that property@name lookups use and
  // that {continue}, {cease} and {break} address an enclosing level by. An explicit name=
  // parameter wins, then a $padForceTagName set by the tag handler, otherwise the tag's own
  // name.

  if ( $padName [$pad] )
    return;

  if ( isset ( $padPrm [$pad] ['name'] ) )
    $padName [$pad] = $padPrm [$pad] ['name'];
  elseif ( $padForceTagName )
    $padName [$pad] = $padForceTagName;
  else
    $padName [$pad] = $padTag [$pad];

?>