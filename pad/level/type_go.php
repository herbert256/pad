<?php

  $TagCnt [$p]++;

  if ( $pTrace )
    include 'trace/tag/before.php';

  $pTagContent = ''; ob_start();

  pTiming_start ('tag');
  $pTagResult = include PAD . "types/" . $pType [$p] . ".php";
  pTiming_end ('tag');

  $pTagContent .= ob_get_clean();

  if ( $pTrace )
    include 'trace/tag/after.php';

  if ( is_object   ( $pTagResult ) ) $pTagResult = pToArray( $pTagResult );
  if ( is_resource ( $pTagResult ) ) $pTagResult = pToArray( $pTagResult );

  if ( $pTagResult === TRUE AND $pTagContent <> '' )
    $pTagResult = $pTagContent;

  if ( is_scalar($pTagResult) and strpos($pTagResult , '@content@') !== FALSE )
    $pTagResult = str_replace('@content@', $pTrue [$p], $pTagResult);

?>