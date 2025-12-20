<?php

  if ( count ($padMyTidySanitize) ) {

    $padSanitizeFlags = 0;

    foreach ( $padMyTidySanitize as $padK )
      $padSanitizeFlags = $padSanitizeFlags | (int) "FILTER_FLAG_$padK";

    $padOutput = filter_var ( $padOutput, FILTER_UNSAFE_RAW, $padSanitizeFlags );

  }

  if ( $padMyTidyTabToSpace )
    $padOutput = str_replace ( "\t", ' ', $padOutput );

  if ( $padMyTidyTrim )
    $padOutput = trim ($padOutput);

  if ( $padMyTidyRemoveWhitespace )
    $padOutput = trim(preg_replace('/>\s+</', '><', $padOutput));

  if ( $padMyTidyNoIndent )
    $padOutput = preg_replace('/^ +/m', '', $padOutput);

  if ( $padMyTidyNoEmptyLines )
    $padOutput = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $padOutput);

  if ( $padMyTidyNoNewLines )
    $padOutput = str_replace ( ["\n", "\r"], '', $padOutput);

?>