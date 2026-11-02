<?php

  // A formula of 18 000 bytes - a visitor's comment drawn as {notation:math $comment/} - took memory
  // with the square of its length and ended the request with "Allowed memory size exhausted";
  // a formula is read up to its limit, and the strict check names one that is longer.

  $formula = str_repeat ( '(a)', 6000 );

?>
