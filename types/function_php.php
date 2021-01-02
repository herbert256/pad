<?php

  pad_timing_start ('app');

  $pad_tag_result = call_user_func_array ($pad_tag, $pad_parms_seq);

  pad_timing_end ('app');

  return $pad_tag_result;

?>