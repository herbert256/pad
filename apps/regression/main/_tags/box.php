<?php

  // Fixture for the custom-tag cases: an application tag that reads a parameter and builds
  // its own content. Nothing else in the suite uses it.

  $label = padTagParm ( 'label', 'none' );

  $padContent = "[$label]";

  return TRUE;

?>
