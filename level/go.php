<?php

  $padTagCnt [$pad]++;

  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";

  if ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  if ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

?>