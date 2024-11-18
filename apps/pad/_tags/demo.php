<?php

  $padTableIdx = padFindIdx ( 'table' );

  if ( $padWalk[$pad] == 'start' ) {

    $padWalk[$pad] = 'end';

    $padSourcex = padColorsString ( trim ( $padContent ) );

    $padDemoCount [$padTableIdx]++;

    if ( $padDemoCount [$padTableIdx] > 1 )
      $padDemoSourcePHP = '';

   return TRUE;

  }

  $firstFile = $secondFile = '';

  $firstFileP  = padTagParm ('firstFile');
  $secondFileP = padTagParm ('secondFile');

  if ( $firstFileP ) {
    $firstFile = '<td style="vertical-align:top">';
    if ( file_exists ( APP . $firstFileP ) and ! is_dir ( APP . $firstFileP ) ) 
      $firstFile .= padColorsFile ( APP . $firstFileP );
    $firstFile .= '</td>';
  }

  if ( $secondFileP ) {
    $secondFile = '<td style="vertical-align:top">';
    if ( file_exists ( APP . $secondFileP ) and ! is_dir ( APP . $secondFileP ) ) 
      $secondFile .= padColorsFile ( APP . $secondFileP );
    $secondFile .= '</td>';
  }

  if ( padTagParm ('pre') )
    $padContent = "<pre>$padContent</pre>";

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