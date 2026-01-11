<?php

  padDeleteDataDir ( DAT . 'examples' );

  set_time_limit ( 60 );

  foreach ( padAppsList () as $one ) {

    extract ( $one );

    $source = padFileGet ( APPS . "$app/$item.pad" );

    if ( str_contains ( $source, '{page'    ) ) continue;
    if ( str_contains ( $source, '{example' ) ) continue;
    if ( str_contains ( $source, '{ajax'    ) ) continue;

    $curl = padCurl ( "$padHost/$app?$item&padExamples" );

    if ( ! str_starts_with ( $curl ['result'], '2' ) )
      continue;

    if ( str_contains ( $source, '{table' ) ) continue;
    if ( str_contains ( $source, '{demo'  ) ) continue;
 
    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    padFilePut ( "examples/$app/$item.pad",  padTidySmall ( $source,        TRUE ) );
    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }

?>