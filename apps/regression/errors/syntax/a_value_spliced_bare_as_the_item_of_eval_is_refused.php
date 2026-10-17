<?php

  // A value spliced bare as the item of {eval} - {eval {$q}} - is a whole expression from a
  // value: quoted by the splice, it was the expression the eval sequence runs, and a request
  // value 0 + php:abs(-3) called PHP.

  $q = '0 + php:abs(-3)';

?>
