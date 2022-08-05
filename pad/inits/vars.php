<?php

  $pad_timings_count = $pad_timings = [];
  $pad_err_cnt =$pad_eval_cnt = $pad_fld_cnt = $pad_lvl_cnt = $pad_opt_cnt = $pad_err_cnt = $pad_type_cnt = 0;
  $pad_field_double_check = $pad_restart = '';

  $pad            = 1;
  $pad_between    = 'start';
  $pad_output     = '';
  $pad_stop       = '000';
  $pad_cache_stop = 0;
  $pad_etag       = '';
  $pad_exit       = 1;
  $pad_len        = 0;
  $pad_time       = $_SERVER['REQUEST_TIME'];  

  $pad_trace_dir = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";
  $pad_level_dir  = "$pad_trace_dir";
  $pad_occur_dir  = "$pad_trace_dir";

  $pad_errror_list = [];

?>