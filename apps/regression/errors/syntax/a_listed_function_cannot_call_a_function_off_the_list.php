<?php

  // array_map is on the list and strtoupper is not: handing strtoupper to array_map as its
  // callback is calling strtoupper.

  $padPhpFunctions = [ 'implode', 'array_map' ];

?>
