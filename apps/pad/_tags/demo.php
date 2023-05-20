<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padWalk[$pad] = 'end';

    $padSourcex = padColorsString (trim($padContent));

    $padDemoCount[$pad-1]++;

    if ( $padDemoCount[$pad-1] > 1 )
      $padDemoSourcePHP = '';

   return TRUE;

  }

  $padContent = 
    '<tr>' .
    $padDemoSourcePHP .
      '<td style="vertical-align:top">' .
      '<!-- START DEMO SOURCE -->' .  
      trim($padSourcex)  . 
      '<!-- END DEMO SOURCE -->' .  
      '</td>' .
      '<td style="vertical-align:top"><code><span style="color: #000000">' . 
      '<!-- START DEMO RESULT -->' .  
      trim($padContent) . 
      '<!-- END DEMO RESULT -->' .  
      '</span></code></td>' .
    '</tr>';

?>
