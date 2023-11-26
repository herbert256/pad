<?php


  function padTail ( $parm1, $parm2='', $parm3='', $parm4='' ) {

    global $padTailCnt, $padTailMeta, $padTailDir;

    if ( ! $padTailMeta )
      return;

    $padTailCnt++;

    $extra = padMakeSafe ( $parm3) . ' ' . padMakeSafe ( $parm4 );

    $line = padTailIds ()
          . sprintf ( '%-15s', padMakeSafe ( $parm1 ) )
          . sprintf ( '%-15s', padMakeSafe ( $parm2 ) )
          . substr ( $extra, 0, 100 );

    padTailLine ( "$padTailDir/tail.txt", $line );

  }


  function padTailIds () {

    global $padTailCnt,$padXrefId, $padTraceLine, $padXmlId;

    return sprintf ( '%-7s',  $padTailCnt )
         . sprintf ( '%-9s',  padTailPadOccur () )
         . sprintf ( '%-16s', "$padTraceLine/$padXrefId/$padXmlId" );

  }


  function padTailPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padTailPut ( $in, $data, $append=0 ) {

    if ( $append ) padTailLine ( $in, $data );
    else           padTailFile ( $in, $data );

  }


  function padTailExists ( $file ) {

    return file_exists ( $file );

  }


  function padTailLine ( $in, $data ) {

    padTailCheck ( $in );

    file_put_contents ( padData . $in, $data . "\n", LOCK_EX | FILE_APPEND );
    
  }


  function padTailFile ( $in, $data ) {

    padTailCheck ( $in );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
    
    file_put_contents ( padData . $in, "$data\n", LOCK_EX );
    
  }


  function padTailCheck ( $file ) {

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      padTailMkDir ( $dir );

    if ( ! file_exists ($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }
    
  }


  function padTailMkDir( $dir ) {

   set_error_handler ( 'padErrorThrow' );

    try {

      mkdir ($dir, $GLOBALS ['padDirMode'], true );

    } catch (Throwable $e) {

  
    }

    restore_error_handler ();  
    
  }


  function padTailGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return file_get_contents ($file);

  }


  function padTailRequestInit ( $file ) {

   if ( function_exists ('getallheaders') )
      $headers = getallheaders();
    else
      $headers = [];

    $input = file_get_contents('php://input') ?? '';

    padTailPut ( $file,  [
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
      padTailPut ( $file,  $input );
    }
      
  }


  function padTailRequestExit ( $file ) {

    global $padOutput;

    flush();

    padTailPut ( $file,  [
        'http'    => http_response_code (),
        'headers' => headers_list () ?? []
      ] );

    if ( $padOutput ) {
      $file = str_replace ( 'exit.json', 'output.txt', $file );
      padTailPut ( $file,  $padOutput );
    }
      
  }


?>