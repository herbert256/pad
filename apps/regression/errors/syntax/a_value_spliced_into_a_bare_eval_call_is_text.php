<?php

  // eval(2, '...') is sequence:eval(2, '...') written without its prefix, and a value spliced
  // into its quoted expression is a string of it there too: it went in bare, and the request
  // value 0 + php:abs(-3) called PHP - the call answered 3.

  $q = '0 + php:abs(-3)';

?>
