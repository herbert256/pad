<?php

  $padTagCnt [$pad]++;

  $padTagContent = ''; ob_start();

  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";

  $padTagContent .= ob_get_clean();

  if ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  if ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );

  if ( $padTagResult === TRUE AND $padTagContent <> '' )
    $padTagResult = $padTagContent;

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

?>