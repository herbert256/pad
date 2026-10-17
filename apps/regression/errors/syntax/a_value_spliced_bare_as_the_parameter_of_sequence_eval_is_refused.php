<?php

  // A value spliced bare as the parameter of sequence:eval() - sequence:eval(2, {$q}) - is a
  // whole expression from a value, refused as sequence:eval(2, $q) is: quoted by the splice,
  // it was a string the template wrote, and the eval sequence ran it.

  $q = '0 + php:abs(-3)';

?>
