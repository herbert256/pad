<?php

  // The options group: look the path $names up in the named options of level $padIdx,
  // the name="value" pairs written on the tag, as read by padTagParm().

  global $padPrm;

  return padAtSearch ( $padPrm [$padIdx], $names );

?>