<?php

  // Hears every remote fetch: the result code, and whether it failed with an error.

  $GLOBALS ['heardCurl'] [] = $result . ( $error ? ' with an error' : ' without an error' );

?>
