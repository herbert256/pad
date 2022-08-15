<?php

  $padTagCnt [$pad]++;

  if ( $padTrace )
    include 'trace/tag/before.php';

  $padTagContent = ''; ob_start();

  pTiming_start ('tag');
  $padTagResult = include PAD . "types/" . $padType [$pad] . ".php";
  pTiming_end ('tag');

  $padTagContent .= ob_get_clean();

  if ( $padTrace )
    include 'trace/tag/after.php';

  if ( is_object   ( $padTagResult ) ) $padTagResult = pToArray( $padTagResult );
  if ( is_resource ( $padTagResult ) ) $padTagResult = pToArray( $padTagResult );

  if ( $padTagResult === TRUE AND $padTagContent <> '' )
    $padTagResult = $padTagContent;

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

?>