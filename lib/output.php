<?php

  function padOutput ( $file ) {

    $padOutput = padUnescape ( $padOutput );

    if ( count ($padOutputSanitize) ) {

      // Default filter options on the complete output, executed before Tidy
      // Must be a flag from FILTER_UNSAFE_RAW from below page.
      // https://www.php.net/manual/en/filter.filters.sanitize.php

      $padSanitizeFlags = 0;

      foreach ( $padOutputSanitize as $padK )
        $padSanitizeFlags = $padSanitizeFlags | (int) "FILTER_FLAG_$padK";

      $padOutput = filter_var ( $padOutput, FILTER_UNSAFE_RAW, $padSanitizeFlags );

    }

    if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE )
      include 'tidy.php';

    if ( $padOutputTabToSpace )
      $padOutput = str_replace ( "\t", '', $padOutput );

    if ( $padOutputTrim )
      $padOutput = trim ($padOutput);

    if ( $padOutputRemoveWhitespace ) 
      $padOutput = trim(preg_replace('/>\s+</', '><', $padOutput));

    if ( $padOutputNoEmptyLines ) 
      $padOutput = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $padOutput);

    if ( $padOutputNoIndent ) 
      $padOutput = preg_replace('/^ +/m', '', $padOutput);

    if ( $padOutputNoNewLines )
      $padOutput = str_replace ( ["\n", "\r"], '', $padOutput);

  }


?>