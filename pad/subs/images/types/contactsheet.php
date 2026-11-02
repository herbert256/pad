<?php

  // One downloadable PNG contact sheet from rows of local image files, with a caption list.
  include_once PAD . 'subs/charts/_lib/chart.php';
  $padImageRows = padChartRows ( padUnprotect ( $padContent ), 'image contact sheet' );
  $padData [$pad] = padDefaultData ();
  return padImageContactSheet ( $padImageRows, padImageOptions ( 200, FALSE ),
    padWidgetNumber ( 'image contactsheet', 'columns', 3 ),
    padParmText ( padTagParm ( 'src', 'src' ) ), padParmText ( padTagParm ( 'caption', 'caption' ) ) );

?>
