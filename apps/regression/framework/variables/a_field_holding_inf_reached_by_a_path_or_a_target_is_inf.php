<?php

  // INF reached by a dotted path or a name@tag target is the value, as the plain field is
  // (a_variable_holding_inf_is_a_value_and_no_missing_field): the @ resolver answered INF for
  // "nothing there", and {$s.m} and {$m@d} ended the page on "Field ... not found".

  $s = [ 'm' => INF, 'n' => 1 ];

?>
