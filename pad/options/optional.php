<?php

  // Implements the optional option: swallows a tag that could not be resolved by handing
  // padLevel() an empty string, so the tag renders as nothing instead of raising an error.
  // Included by level/no.php, the path taken when no type matched the tag name.

  padLevel ( '' );

?>