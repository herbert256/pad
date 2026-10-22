<?php

  // INF is a value in a tag's answer as it is in a field (a_variable_holding_inf_is_a_value_
  // and_no_missing_field): {echo $big}, its pipes and ?? printed nothing, as a tag that
  // answered no value, and {debug $big} called it missing.

  $big = INF;
  $neg = -INF;

?>
