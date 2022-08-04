<?php

  $pad_timings_count = $pad_timings = [];
  $pad_err_cnt =$pad_eval_cnt = $pad_fld_cnt = $pad_lvl_cnt = $pad_opt_cnt = $pad_err_cnt = $pad_type_cnt = 0;
  $pad_field_double_check = $pad_restart = '';

  $pad_lvl        = 1;
  $pad_output     = '';
  $pad_stop       = '000';
  $pad_cache_stop = 0;
  $pad_etag       = '';
  $pad_exit       = 1;
  $pad_len        = 0;
  $pad_time       = $_SERVER['REQUEST_TIME'];  

  $pad_trace_dir_base = "trace/$app-" . str_replace('/', '-', $page) . "/$PADREQID";
  $pad_trace_dir_lvl  = "$pad_trace_dir_base";
  $pad_trace_dir_occ  = "$pad_trace_dir_base";

?>