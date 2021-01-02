<?php

  $pad_option_type  = substr ( $pad_options, 8, 5);
  $pad_option_event = substr ( $pad_options, 6);

  if     ( $pad_options == 'level_start'                              ) $pad_content = $pad_base   [$pad_lvl];
  elseif ( $pad_options == 'level_tag' and $pad_parms_type == 'open'  ) $pad_content = $pad_base   [$pad_lvl];
  elseif ( $pad_options == 'level_tag' and $pad_parms_type == 'close' ) $pad_content = $pad_result [$pad_lvl];
  elseif ( $pad_options == 'level_end'                                ) $pad_content = $pad_result [$pad_lvl];
  elseif ( $pad_options == 'occur_start'                              ) $pad_content = $pad_html   [$pad_lvl];
  elseif ( $pad_options == 'occur_tag' and $pad_parms_type == 'open'  ) $pad_content = $pad_html   [$pad_lvl];
  elseif ( $pad_options == 'occur_tag' and $pad_parms_type == 'close' ) $pad_content = $pad_html   [$pad_lvl];
  elseif ( $pad_options == 'occur_end'                                ) $pad_content = $pad_html   [$pad_lvl];

  foreach ( $GLOBALS["pad_parms_$pad_options"] as $pad_option_name )
    if ( isset ( $pad_parms_tag [$pad_option_name] ) )
      include PAD_HOME . "parms/$pad_option_name.php" ;

  if     ($pad_options == 'level_start'                              ) $pad_base   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'level_tag' and $pad_parms_type == 'open'  ) $pad_base   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'level_tag' and $pad_parms_type == 'close' ) $pad_result [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'level_end'                                ) $pad_result [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_start'                              ) $pad_html   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_tag' and $pad_parms_type == 'open'  ) $pad_html   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_tag' and $pad_parms_type == 'close' ) $pad_html   [$pad_lvl] = $pad_content;
  elseif ($pad_options == 'occur_end'                                ) $pad_html   [$pad_lvl] = $pad_content;

?>