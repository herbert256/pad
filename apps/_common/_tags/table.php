<?php

  if ( $padWalk[$pad] == 'start' ) {

    $tablePAD  = '';
    $tableHTML = '';

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

  if ( $tablePAD and $tableHTML and isset ( $_REQUEST ['padExamples'] ) ) {

    $tablePADcount [$padPage] = $tablePADcount [$padPage] ?? 0;
    $tablePADcount [$padPage]++;

    $tableITEM = "examples/$padApp/$padPage" . '_' . $tablePADcount [$padPage];

    padFilePut ( "$tableITEM.pad",  padTidySmall ( $tablePAD,  TRUE ) );
    padFilePut ( "$tableITEM.html", padTidySmall ( $tableHTML, TRUE ) );

  }

  $padContent = str_replace ( '@ROWSPAN@', $padDemoCount [$pad] , $padContent);

?>