<?php

  if ( $pad_walk == 'start' ) {

    $pad_demo_source [$pad_lvl] = $pad_content;

    $pad_walk = 'end';

   return TRUE;

  }

  if ( $pad_walk == 'end' ) {

    return 
      '<!-- demo -->' . 
      '<tr>' .
        '<td style="vertical-align:top">' .  pad_colors_string (trim($pad_demo_source [$pad_lvl])) . '</td>' .
        '<td style="vertical-align:top">' .  trim($pad_result [$pad_lvl]) .                          '</td>' .
      '</tr>';

  }
 
?>