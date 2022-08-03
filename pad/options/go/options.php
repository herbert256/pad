<?php

  pad_timing_start ('opt');

  $pad_options_walk = $GLOBALS["pad_options_$pad_options"];
   
  if     ( $pad_options == 'start' ) $pad_content = $pad_base   [$pad_lvl];
  elseif ( $pad_options == 'end'   ) $pad_content = $pad_result [$pad_lvl];

  foreach ( $pad_parms_tag as $pad_option_name => $pad_v )

    if ( in_array ( $pad_option_name, $pad_options_walk ) and ! isset ( $pad_options_done [$pad_option_name] ) ) {

      $pad_options_done [$pad_option_name] = TRUE;

      include PAD . "options/$pad_option_name.php" ;

      if ($pad_trace_options)
        include 'trace';

    }

  if     ($pad_options == 'start' ) $pad_base   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'end'   ) $pad_result [$pad_lvl] = $pad_content;

  pad_timing_end ('opt');

?>