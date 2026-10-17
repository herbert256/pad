<?php

  // A value spliced bare as the whole of where= - where={$cond} - is a whole condition from
  // a value, refused as where=$cond is: quoted by the splice, it was the per-row expression.

  $cond = '$n eq php:abs(-3)';

?>
