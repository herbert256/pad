<?php

  if ( $pWalk[$p] == 'start' ) {

    $pDemo_source = pColors_string (trim($pContent));

    $pWalk[$p] = 'end';

   return TRUE;

  }

  $pContent = 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  $pDemo_source   . '</td>' .
      '<td style="vertical-align:top">' .  trim($pContent) . '</td>' .
    '</tr>';
 
?>