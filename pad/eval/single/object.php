<?php

  // object: - reads the PHP variable called $name (a variable variable, so whatever the page's
  // .php file left in scope) and flattens it to an array, so an object or resource can be
  // walked like ordinary PAD data.

  return padToArray($$name);

?>