<?php

  $padTagCnt [$pad]++;

  $padExtraContent = '';

  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";

  if ( is_scalar($padTagResult) and strpos($padTagResult , '@content@') !== FALSE )
    $padTagResult = str_replace('@content@', $padTrue [$pad], $padTagResult);

  $padContent .= $padExtraContent;

?>