<?php

  if ( $pWalk[$p] == 'start' ) {

    $pDemo_source [$p] = $pContent;

    $pad_walk = 'end';

   return TRUE;

  }

  return 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  pColors_string (trim($pDemo_source [$p])) . '</td>' .
      '<td style="vertical-align:top">' .  trim($pResult [$p]) .                          '</td>' .
    '</tr>';
 
?>