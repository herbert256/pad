<?php


  function padInfoError ($error, $file, $line) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDir ( "$file:$line $error", $GLOBALS ['padInfoDir'] . '/ERROR');

      unset ( $GLOBALS ['padDumpToDirDone'] );
    
    } catch (Throwable $e) {
    
      // ignore

    }

    restore_error_handler ();   

  }


  function padInfoIds () {

    global $padXrefId, $padTraceId, $padXmlId;

    return sprintf ( '%-9s',  padInfoPadOccur () )
         . sprintf ( '%-16s', "$padTraceId/$padXrefId/$padXmlId" );

  }


  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padInfoLine ( $in, $data ) {

    padInfoWrite ( $in, $data, 1); 
    
  }


  function padInfoFile ( $in, $data ) {
    
    padInfoWrite ( $in, $data, 0); 
    
  }


  function padInfoWrite ( $in, $data, $append=0 ) {

    global $padInfoDir;

    padInfoCheck ( "$padInfoDir/$in" );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
    
    if ( $append)
      file_put_contents ( padData . "$padInfoDir/$in", $data . "\n", LOCK_EX | FILE_APPEND );
    else
      file_put_contents ( padData . "$padInfoDir/$in", "$data",      LOCK_EX );
    
  }


  function padInfoCheck ( $file ) {

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      padInfoMkDir ( $dir );

    if ( ! file_exists ($file) ) {      
      touch ($file);
      chmod ($file, $GLOBALS ['padFileMode']);
    }
    
  }


  function padInfoMkDir( $dir ) {

   set_error_handler ( 'padErrorThrow' );

    try {

      mkdir ($dir, $GLOBALS ['padDirMode'], true );

    } catch (Throwable $e) {
  
    }

    restore_error_handler ();  
    
  }


  function padInfoGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return file_get_contents ($file);

  }

?>