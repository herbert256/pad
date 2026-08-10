<?php

  // The options group: look the path $names up in the named options of level $padIdx,
  // the name="value" pairs written on the tag, as read by padTagParm().

  global $padPrm;

  foreach ( array_keys ( $padPrm [$padIdx] ) as $padAtOptionsOne )
    padDoneAt ( $padIdx, $padAtOptionsOne );

  return padAtSearch ( $padPrm [$padIdx], $names );

?>