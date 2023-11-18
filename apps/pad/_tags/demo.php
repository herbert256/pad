<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padWalk[$pad] = 'end';

    $padSourcex = padColorsString (trim($padContent));

    $padDemoCount[$pad-1]++;

    if ( $padDemoCount[$pad-1] > 1 )
      $padDemoSourcePHP = '';

   return TRUE;

  }

  $firstFile = $secondFile = '';

  $firstFileP  = padTagParm ('firstFile');
  $secondFileP = padTagParm ('secondFile');

  if ( $firstFileP ) {
    $firstFile = '<td style="vertical-align:top">';
    if ( file_exists ( padApp . $firstFileP ) and ! is_dir ( padApp . $firstFileP ) ) 
      $firstFile .= padColorsFile ( padApp . $firstFileP );
    $firstFile .= '</td>';
  }

  if ( $secondFileP ) {
    $secondFile = '<td style="vertical-align:top">';
    if ( file_exists ( padApp . $secondFileP ) and ! is_dir ( padApp . $secondFileP ) ) 
      $secondFile .= padColorsFile ( padApp . $secondFileP );
    $secondFile .= '</td>';
  }

  $padContent = 
    '<tr>' .
      $firstFile .
      $secondFile .
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