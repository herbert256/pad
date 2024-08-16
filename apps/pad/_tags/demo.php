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
    if ( file_exists ( '/app/' . $firstFileP ) and ! is_dir ( '/app/' . $firstFileP ) ) 
      $firstFile .= padColorsFile ( '/app/' . $firstFileP );
    $firstFile .= '</td>';
  }

  if ( $secondFileP ) {
    $secondFile = '<td style="vertical-align:top">';
    if ( file_exists ( '/app/' . $secondFileP ) and ! is_dir ( '/app/' . $secondFileP ) ) 
      $secondFile .= padColorsFile ( '/app/' . $secondFileP );
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