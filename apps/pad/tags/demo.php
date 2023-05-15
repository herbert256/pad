<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padSourcex = padColorsString (trim($padContent));

    $padWalk[$pad] = 'end';

   return TRUE;

  }

  $padContent = 
    '<tr>' .
      '<td style="vertical-align:top">' .  trim($padSourcex)  . '</td>' .
      '<td style="vertical-align:top"><code><span style="color: #000000">' . trim($padContent) . '</span></code></td>' .
    '</tr>';

?>