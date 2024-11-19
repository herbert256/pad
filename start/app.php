<?php



  // Base of the PAD repository with all files.

  if ( ! isset ( $padHome ) ) 
    include 'start/home.php';



  // When used in a HTTP server like Apache.

  $padApp = $_SERVER ['REQUEST_URI'] ?? '';

  $padApp = str_replace ( '.php', '', $padApp );

  if ( str_contains ( $padApp, '?' ) )
    $padApp = substr ($padApp, 0, strpos($padApp, '?') );

  $padFind = explode ( '/', $padApp );

  foreach ( $padFind as $padCheck )
    if ( $padCheck and file_exists ("$padHome/apps/$padCheck/") ) {
      $padApp = "$padHome/apps/$padCheck/";
      return;
    }



  // Below will work with CLI

  $padApp = $_SERVER ['SCRIPT_FILENAME'] ?? '';

  $padFind = explode ( '/', $padApp );

  foreach ( $padFind as $padCheck )
    if ( $padCheck and file_exists ("$padHome/apps/$padCheck/") ) {
      $padApp = "$padHome/apps/$padCheck/";
      return;
    }



  // Maybe the Default PAD app PAD is there.

  if ( file_exists ( "$padHome/apps/pad/" ) ) {
    $padApp = "$padHome/apps/pad/";
    return;
  }



  // Nothing found.

  die ( 'No PAD application found' );



?>