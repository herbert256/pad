<?php

  pad_timing_start ('opt');

  $pad_option_type  = substr ( $pad_options, 0, 5);
  $pad_option_event = substr ( $pad_options, 6);

  $pad_options_walk = $GLOBALS["pad_parms_$pad_options"];
   
  if     ( $pad_options == 'level_start' ) $pad_content = $pad_base   [$pad_lvl];
  elseif ( $pad_options == 'level_end'   ) $pad_content = $pad_result [$pad_lvl];

  foreach ( $pad_parms_tag as $pad_option_name => $pad_v ) {

    if ( in_array ( $pad_option_name, $pad_options_walk ) and ! isset ( $pad_options_done [$pad_option_name] ) ) {

      $pad_opt_cnt++;

      $pad_options_done [$pad_option_name] = TRUE;
      include PAD . "options/$pad_option_name.php" ;

      if ($pad_trace_options) {
        $pad_options_dir = "$pad_trace_dir_lvl/options/$pad_option_name-$pad_opt_cnt";
        pad_file_put_contents ( "$pad_options_dir/data.html", $pad_data[$pad_lvl] );
        pad_file_put_contents ( "$pad_options_dir/content.html", $pad_content );
        pad_file_put_contents ( "$pad_options_dir/base.html", $pad_base[$pad_lvl] );
      }

    }

    if ( in_array($pad_option_name, $pad_parms_level_start) and 
         in_array($pad_option_name, $pad_parms_level_end) )
      unset ( $pad_options_done [$pad_option_name]);

  }

  if     ($pad_options == 'level_start' ) $pad_base   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'level_end'   ) $pad_result [$pad_lvl] = $pad_content;

  pad_timing_end ('opt');

?>