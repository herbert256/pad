<?php

  pad_timing_start ('app');

  $name_result = call_user_func_array ($name, $parms);

  pad_timing_end ('app');

  return $name_result;

?>