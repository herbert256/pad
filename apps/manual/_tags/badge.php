<?php

  // Fixture for the extending page: an application tag that reads a parameter and builds
  // its own content.

  $label = padTagParm ( 'label', 'plain' );

  $padContent = "&laquo;$label&raquo;";

  return TRUE;

?>