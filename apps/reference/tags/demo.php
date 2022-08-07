<?php

  if ( $pad_walk == 'start' ) {

    $pDemo_source [$pad] = $pContent;

    $pad_walk = 'end';

   return TRUE;

  }

  return 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  pColors_string (trim($pDemo_source [$pad])) . '</td>' .
      '<td style="vertical-align:top">' .  trim($pResult [$pad]) .                          '</td>' .
    '</tr>';
 
?>