<?php

  if ( ! pad_local() ) 
    return;

  if ($pad_demo_count)
    $pad_demo_first = '';
  else
    $pad_demo_first = '###first###';

  if ( $pad_lvl > 1 ) {

    $pad_result [$pad_lvl] = 
      '<tr>' . $pad_demo_first .
        '<td style="vertical-align:top">' .  pad_colors_string ($pad_demo_source [$pad_lvl]) . '</td>' .
        '<td style="vertical-align:top">' .  $pad_result [$pad_lvl] .                          '</td>' .
      '</tr>';
 
    $pad_demo_count++;
  
  }

?>