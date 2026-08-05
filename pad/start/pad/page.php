<?php

  // Supplies the base for a nested pass whose source is a page: build/build.php assembles
  // the wrappers, _lib includes and template of $padPage into the fresh level and starts
  // iterating it. Reached from start/pad/pad.php when $padStrBld is 'page' - both for the
  // request's own page and for each {page} tag.

  include PAD . 'build/build.php';

?>