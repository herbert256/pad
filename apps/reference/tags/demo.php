<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padSourcex = padColorsString (trim($padContent));

    $padWalk[$pad] = 'end';

   return TRUE;

  }

  $padContent = 
    '<!-- demo -->' . 
    '<tr>' .
      '<td style="vertical-align:top">' .  trim($padSourcex)  . '</td>' .
      '<td style="vertical-align:top">' .  trim($padContent) . '</td>' .
    '</tr>';
 
?>