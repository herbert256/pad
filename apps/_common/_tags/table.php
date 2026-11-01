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

  if ( $tablePAD and $tableHTML and padSelfSwitch ( 'padExamples' ) ) {

    $tablePADcount [$padPage] = $tablePADcount [$padPage] ?? 0;
    $tablePADcount [$padPage]++;

    $tableITEM = "examples/$padApp/$padPage" . '_' . $tablePADcount [$padPage];

    padFilePut ( "$tableITEM.pad",  padTidySmall ( $tablePAD,  TRUE ) );
    padFilePut ( "$tableITEM.html", padTidySmall ( $tableHTML, TRUE ) );

  }

  $padContent = str_replace ( '@ROWSPAN@', $padDemoCount [$pad] , $padContent);

  // The demos of a table are one example - each runs after the ones above it, with what they
  // left - so the icon at the table's top right takes all of them to the playground, with
  // the page's PHP when the table shows it (_lib/playground.php).

  $tablePlay = ( $tablePAD !== '' )
             ? padPlayLink ( trim ( $tablePAD ) . "\n", '', padTagParm ( 'withPHP' ) ? (string) padFileGet ( APP . "$padPage.php" ) : '' )
             : '';

  $padContent = str_replace ( '@PLAY@', $tablePlay, $padContent );

?>
