<?php

  $pad_opt_cnt++;

  $pad_options_dir = "$pad_level_dir/options/$pad_option_name-$pad_opt_cnt";

  pad_file_put_contents ( "$pad_options_dir/data.html",    $pad_data[$pad] );
  pad_file_put_contents ( "$pad_options_dir/content.html", $pad_content );
  pad_file_put_contents ( "$pad_options_dir/base.html",    $pad_base[$pad] );

?>