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
      mkdir ($dir, $GLOBALS ['padDirMode'], true );

    if ( ! file_exists ($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }
    
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

    padTailPut ( $file,  [
        'get'     => $_GET ??    '',
        'post'    => $_POST ??   '',
        'cookies' => $_COOKIE ?? '',
        'input'   => file_get_contents('php://input') ?? '',
        'headers' => $headers,
        'files '  => $_FILES ?? '',
        'server'  => $_SERVER ?? '',
        'request' => $_REQUEST ?? '',
        'session' => $_SESSION ?? '',
        '_ENV'    => $_ENV ?? '',
        'getenv'  => getenv () ?? '' ] );
      
  }


  function padTailRequestExit ( $file ) {

    padTailPut ( $file,  [
        'headers' => headers_list () ?? [],
        'output'  => $GLOBALS ['padOutput'] ?? ''
      ] );
      
  }


?>