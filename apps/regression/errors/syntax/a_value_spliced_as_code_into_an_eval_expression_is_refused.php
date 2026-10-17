<?php

  // A value that stands in the expression of the eval sequence outside the expression's own
  // strings is refused by the sequence itself, whatever the spelling - here the eval option
  // after its item, which no guard of the splice can see coming: the request value
  // 0 + php:abs(-3) called PHP, 3 3.

  $q = '0 + php:abs(-3)';

?>
