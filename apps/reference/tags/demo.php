<?php

  if ( $pWalk[$p] == 'start' ) {

    $pSourcex = pColors_string (trim($pContent));

    $pWalk[$p] = 'end';

   return TRUE;

  }

  $pContent = 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  trim($pSourcex)  . '</td>' .
      '<td style="vertical-align:top">' .  trim($pContent) . '</td>' .
    '</tr>';
 
?>