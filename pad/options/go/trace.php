<?php

  $pad_opt_cnt++;

  $pad_options_dir = "$pad_trace_dir_lvl/options/$pad_option_name-$pad_opt_cnt";

  pad_file_put_contents ( "$pad_options_dir/data.html",    $pad_data[$pad_lvl] );
  pad_file_put_contents ( "$pad_options_dir/content.html", $pad_content );
  pad_file_put_contents ( "$pad_options_dir/base.html",    $pad_base[$pad_lvl] );

?>