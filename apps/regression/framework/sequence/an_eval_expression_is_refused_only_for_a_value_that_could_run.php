<?php

  // The eval sequence refuses an expression only for a spliced value that could call code or
  // read a variable - a (, a $ or a : in it: a - or a * 2 printed in a tag's parameters
  // elsewhere on the page, and a {define} written into the expression, made eval='@ - 1',
  // eval='@ * 2' and eval='@ {twice}' refusals.

  $op   = '* 2';
  $dash = '-';

?>
