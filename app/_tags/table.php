<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padWalk[$pad] = 'end';

    $padDemoCount [$pad] = 0;

    if ( padTagParm ( 'withPHP' ) ) 

      $padDemoSourcePHP = 
        '<td style="vertical-align:top" rowspan="@ROWSPAN@">' .  
          padColorsFile ( APP . "$padPage.php" )  . 
        '</td>';

    else

      $padDemoSourcePHP = '';
 
    return TRUE;

  }

  $padContent = str_replace ( '@ROWSPAN@', $padDemoCount [$pad] , $padContent);

?>