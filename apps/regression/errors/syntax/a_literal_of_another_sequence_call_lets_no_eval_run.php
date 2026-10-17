<?php

  // Only a literal parameter of the eval sequence is an expression the template wrote: the
  // value spliced into sequence:repeat(2, '...') was taken for one, and sequence:eval(2, $q),
  // refused on its own, then ran the same value from the request.

  $q = '0 + php:abs(-3)';

?>
