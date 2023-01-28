<?php

  $padTagCnt [$pad]++;

  if ( $padTrace )
    include 'trace/tag/before.php';

  $padTagContent = ''; ob_start();

  padTimingStart ('tag');
  $padTagResult = include PAD . "pad/types/" . $padType [$pad] . ".php";
  padTimingEnd ('tag');

  $padTagContent .= ob_get_clean();

  if ( $padTrace )
    include 'trace/tag/after.php';

  if ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  if ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );

  if ( $padTagResult === TRUE AND $padTagContent <> '' )
    $padTagResult = $padTagContent;

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

?>