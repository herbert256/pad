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
      '<td style="vertical-align:top">' .  trim($padSourcex)  . '</td>' .
      '<td style="vertical-align:top"><code><span style="color: #000000">' . trim($padContent) . '</span></code></td>' .
    '</tr>';

?>
