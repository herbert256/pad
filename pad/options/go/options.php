<?php

  $pad_option_type  = substr ( $pad_options, 0, 5);
  $pad_option_event = substr ( $pad_options, 6);

  $pad_options_walk = $GLOBALS["pad_parms_$pad_options"];

  if ( $pad_options == $pad_demand_level ) $pad_options_walk = array_merge ( $pad_options_walk, $pad_parms_demand_level);
  if ( $pad_options == $pad_demand_occur ) $pad_options_walk = array_merge ( $pad_options_walk, $pad_parms_demand_occur);
  if ( $pad_options == $pad_demand )       $pad_options_walk = array_merge ( $pad_options_walk, $pad_parms_demand );
   
  if     ( $pad_options == 'level_start' ) $pad_content = $pad_base   [$pad_lvl];
  elseif ( $pad_options == 'level_end'   ) $pad_content = $pad_result [$pad_lvl];
  elseif ( $pad_options == 'occur_start' ) $pad_content = $pad_html   [$pad_lvl];
  elseif ( $pad_options == 'occur_end'   ) $pad_content = $pad_html   [$pad_lvl];

  foreach ( $pad_parms_tag as $pad_option_name => $pad_v )
    if ( in_array ( $pad_option_name, $pad_options_walk ) )
      if ( ! isset ( $pad_options_done [$pad_option_name] ) ) {
        $pad_options_done [$pad_option_name] = TRUE;
        include PAD_HOME . "pad/options/$pad_option_name.php" ;
      }

  if     ($pad_options == 'level_start' ) $pad_base   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'level_end'   ) $pad_result [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_start' ) $pad_html   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_end'   ) $pad_html   [$pad_lvl] = $pad_content;

?>