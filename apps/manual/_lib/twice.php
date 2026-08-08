<?php

  // Fixture for the extending page: _lib files are included on every request, so a page's
  // PHP can call this without an include of its own.

  function manualTwice ( $value ) {

    return "$value $value";

  }

?>
