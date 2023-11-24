<?php


  function padExists ( $file ) {

    if ( $GLOBALS ['padTraceActive'] and $GLOBALS ['padTraceExists'] ) {

      $return = file_exists ( $file );

      include pad . 'tail/types/trace/items/exists.php';
      
      return $return;
    
    }

    return file_exists ( $file );
    
    if ( ! padValidFile ( $file ) )
      return FALSE;
    else
      return file_exists ( $file );

  }

  function padExistsTrace ( $file ) {


  }


  function padIsDir ( $dir ) {

    if ( ! padValidFile ( $dir ) )
      return FALSE;
    else
      return is_dir ( $dir );

  }


  function padFileCorrect ( $file ) {

    $file = str_replace ( ':', '_', $file );
    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    return $file;

  }


  function padFileGetContents ( $file ) {

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/types/trace/items/get.php';

    if ( is_dir ($file) )
      return $file;

    if ( ! padExists($file) )
      return '';

    return file_get_contents ($file);

  }


  function padFilePutContents ($in, $data='', $append=0) {

    global $pad;
    
    $file = padData . str_replace(':', '_', $in);

    $file = padFileCorrect ( $file );

    if ( ! padValidFile ( $file ) )
      return padError ("Invalid file name: $file");

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data)
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data,     LOCK_EX);
    
  }


  function padFileChkDir ( $file ) {

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! padExists($dir) )
      mkdir ($dir, $GLOBALS ['padDirMode'], true );

  }


  function padFileChkFile ( $file ) {

    if ( ! padExists($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }

  }


?>