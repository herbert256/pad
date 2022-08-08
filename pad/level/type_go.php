<?php

  $pTagCnt [$p]++;

  if ( $pTrace )
    include 'trace/tag/before.php';

  $pTagContent = ''; ob_start();

  pTiming_start ('tag');
  $pTagResult [$p] = include PAD . "types/" . $pType [$p] . ".php";
  pTiming_end ('tag');

  $pTagContent .= ob_get_clean();

  if ( $pTrace )
    include 'trace/tag/after.php';

  if ( is_object   ( $pTagResult [$p] ) ) $pTagResult [$p] = pToArray( $pTagResult [$p] );
  if ( is_resource ( $pTagResult [$p] ) ) $pTagResult [$p] = pToArray( $pTagResult [$p] );

  if ( $pTagResult [$p] === TRUE AND $pTagContent <> '' )
    $pTagResult [$p] = $pTagContent;

  if ( is_scalar($pTagResult [$p]) and strpos($pTagResult [$p] , '@content@') !== FALSE )
    $pTagResult [$p] = str_replace('@content@', $pTrue [$p], $pTagResult [$p]);

?>