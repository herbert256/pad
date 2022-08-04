<?php

  if ( $pad_walk == 'start' ) {

    $pad_demo_source [$pad] = $pad_content;

    $pad_walk = 'end';

   return TRUE;

  }

  return 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  pad_colors_string (trim($pad_demo_source [$pad])) . '</td>' .
      '<td style="vertical-align:top">' .  trim($pad_result [$pad]) .                          '</td>' .
    '</tr>';
 
?>