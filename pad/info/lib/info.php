<?php


  function padErrorInfoError ($error, $file, $line) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padDumpToDir ( "$file:$line $error", $GLOBALS ['padInfoDir'] . '/ERROR');

      unset ( $GLOBALS ['padDumpToDirDone'] );
    
    } catch (Throwable $e) {
    
      // ignore

    }

    restore_error_handler ();   

  }


  function padInfo ( $parm1, $parm2='', $parm3='', $parm4='' ) {

    global $padInfoCnt, $padMain, $padMainMeta, $padInfoDir;

    if ( ! $padMain or ! $padMainMeta )
      return;

    $padInfoCnt++;

    $extra = padMakeSafe ( $parm3) . ' ' . padMakeSafe ( $parm4 );

    $line = padInfoIds ()
          . sprintf ( '%-15s', padMakeSafe ( $parm1 ) )
          . sprintf ( '%-15s', padMakeSafe ( $parm2 ) )
          . substr ( $extra, 0, 100 );

    padInfoLine ( "$padInfoDir/info.txt", $line );

  }


  function padInfoIds () {

    global $padInfoCnt,$padXrefId, $padTraceLine, $padXmlId;

    return sprintf ( '%-7s',  $padInfoCnt )
         . sprintf ( '%-9s',  padInfoPadOccur () )
         . sprintf ( '%-16s', "$padTraceLine/$padXrefId/$padXmlId" );

  }


  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padInfoExists ( $file ) {

    return file_exists ( $file );

  }


  function padInfoLine ( $in, $data ) {

    padInfoCheck ( $in );

    file_put_contents ( padData . $in, $data . "\n", LOCK_EX | FILE_APPEND );
    
  }


  function padInfoFile ( $in, $data ) {

    padInfoCheck ( $in );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
    
    file_put_contents ( padData . $in, "$data\n", LOCK_EX );
    
  }


  function padInfoCheck ( $file ) {

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      padInfoMkDir ( $dir );

    if ( ! file_exists ($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
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


  function padMainRequestInit ( $file ) {

   if ( function_exists ('getallheaders') )
      $headers = getallheaders();
    else
      $headers = [];

    $input = file_get_contents('php://input') ?? '';

    padInfoFile( $file,  [
        'headers' => $headers,
        'get'     => $_GET ??    '',
        'post'    => $_POST ??   '',
        'cookies' => $_COOKIE ?? '',
        'files '  => $_FILES ?? '',
        'server'  => $_SERVER ?? '',
        'session' => $_SESSION ?? '',
        'getenv'  => getenv () ?? '' , 
        'request' => $_REQUEST ?? '' ] );

    if ( $input ) {
      $file = str_replace ( 'entry.json', 'input.txt', $file );
      padInfoFile ( $file,  $input );
    }
      
  }


  function padMainRequestExit ( $file ) {

    global $padOutput;

    $phpHeaders = headers_list ()         ?? [];
    $padHeaders = $GLOBALS ['padHeaders'] ?? [];

    foreach ( $padHeaders as $header ) {
      $key = array_search ( $header, $phpHeaders );
      if ( $key !== FALSE )
        unset ( $phpHeaders [$key] );
    }

    padInfoFile ( $file,  [
        'http'       => http_response_code () ?? 'null',
        'phpHeaders' => $phpHeaders,
        'padHeaders' => $padHeaders
      ] );

    if ( $padOutput ) {
      $file = str_replace ( 'exit.json', 'output.txt', $file );
      padInfoFile ( $file,  $padOutput );
    }
      
  }


?>