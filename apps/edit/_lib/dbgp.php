<?php

  // DBGp, the protocol of Xdebug's step debugger, as far as the editor speaks it. Plain PHP
  // with nothing of PAD in it: the editor's debugger process (_bin/debugger.php) runs on
  // the command line without the engine and includes this file as it is.
  //
  // Xdebug writes packets "<length>\0<xml>\0"; a command goes the other way as one line,
  // "<name> -i <transaction id> [-x value ...] [-- <base64 data>]\0". Its XML carries a
  // default namespace and an xdebug: prefix; both are taken off before SimpleXML reads it,
  // so an element is found by its plain name.
  //
  // dbgpFrames    the complete packets in a buffer, taken out of it
  // dbgpParse     a packet as SimpleXML
  // dbgpCommand   a command line
  // dbgpPath      a file:// URI as a path, dbgpUri the other way
  // dbgpProperty  a <property> - a variable, an eval result - as an array the browser shows
  // dbgpStack     the frames of a stack_get answer
  // dbgpError     the message of an error answer, '' when there is none
  // dbgpLocation  where a break happened: [ file, line ]

  function dbgpFrames ( &$buffer ) {

    $frames = [];

    while ( ( $zero = strpos ( $buffer, "\0" ) ) !== FALSE ) {

      $length = (int) substr ( $buffer, 0, $zero );

      if ( strlen ( $buffer ) < $zero + 1 + $length + 1 )
        break;

      $frames [] = substr ( $buffer, $zero + 1, $length );
      $buffer    = (string) substr ( $buffer, $zero + 1 + $length + 1 );

    }

    return $frames;

  }

  function dbgpParse ( $xml ) {

    $xml = preg_replace ( '/\sxmlns(?::\w+)?="[^"]*"/', '', (string) $xml );
    $xml = preg_replace ( '/(<\/?|\s)xdebug:/', '$1xdebug_', $xml );

    $previous = libxml_use_internal_errors ( TRUE );
    $parsed   = simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA );

    libxml_use_internal_errors ( $previous );

    return $parsed === FALSE ? NULL : $parsed;

  }

  // A value is quoted when it holds a space, a quote or nothing - a property name like
  // $row['first name'].

  function dbgpCommand ( $name, $transaction, $args = [], $data = NULL ) {

    $line = $name . ' -i ' . (int) $transaction;

    foreach ( $args as $flag => $value ) {
      $value = (string) $value;
      if ( $value === '' or preg_match ( '/[\s"\\\\]/', $value ) )
        $value = '"' . str_replace ( [ '\\', '"' ], [ '\\\\', '\\"' ], $value ) . '"';
      $line .= " -$flag $value";
    }

    if ( $data !== NULL )
      $line .= ' -- ' . base64_encode ( (string) $data );

    return $line . "\0";

  }

  function dbgpPath ( $uri ) {

    $uri = (string) $uri;

    return str_starts_with ( $uri, 'file://' ) ? rawurldecode ( substr ( $uri, 7 ) ) : $uri;

  }

  function dbgpUri ( $path ) {

    return 'file://' . str_replace ( '%2F', '/', rawurlencode ( (string) $path ) );

  }

  function dbgpProperty ( $property, $max = 4000 ) {

    $attr     = $property->attributes ();
    $type     = (string) ( $attr ['type'] ?? '' );
    $children = (int) ( $attr ['children'] ?? 0 ) === 1;
    $value    = (string) $property;

    if ( (string) ( $attr ['encoding'] ?? '' ) === 'base64' )
      $value = (string) base64_decode ( $value );

    if ( $children or $type === 'array' or $type === 'object' )
      $value = '';

    $cut = strlen ( $value ) > $max;

    if ( $cut )
      $value = substr ( $value, 0, $max );

    if ( ! mb_check_encoding ( $value, 'UTF-8' ) )
      $value = mb_scrub ( $value, 'UTF-8' );

    $out = [ 'name'        => (string) ( $attr ['name'] ?? '' ),
             'fullname'    => (string) ( $attr ['fullname'] ?? ( $attr ['name'] ?? '' ) ),
             'type'        => $type,
             'classname'   => (string) ( $attr ['classname'] ?? '' ),
             'value'       => $value,
             'size'        => (int) ( $attr ['size'] ?? strlen ( $value ) ),
             'cut'         => $cut,
             'children'    => $children,
             'numchildren' => (int) ( $attr ['numchildren'] ?? 0 ),
             'page'        => (int) ( $attr ['page'] ?? 0 ),
             'pagesize'    => (int) ( $attr ['pagesize'] ?? 0 ),
             'items'       => NULL ];

    if ( isset ( $property->property ) ) {
      $out ['items'] = [];
      foreach ( $property->property as $child )
        $out ['items'] [] = dbgpProperty ( $child, $max );
    }

    return $out;

  }

  function dbgpStack ( $response ) {

    $frames = [];

    foreach ( $response->stack ?? [] as $frame ) {
      $attr = $frame->attributes ();
      $frames [] = [ 'level' => (int) $attr ['level'], 'where' => (string) $attr ['where'], 'type' => (string) $attr ['type'],
                     'file' => dbgpPath ( (string) $attr ['filename'] ), 'line' => (int) $attr ['lineno'] ];
    }

    return $frames;

  }

  function dbgpError ( $response ) {

    if ( ! isset ( $response->error ) )
      return '';

    $message = trim ( (string) ( $response->error->message ?? '' ) );

    return $message !== '' ? $message : 'error ' . (string) $response->error ['code'];

  }

  function dbgpLocation ( $response ) {

    $message = $response->xdebug_message ?? NULL;

    if ( ! $message )
      return NULL;

    return [ 'file' => dbgpPath ( (string) $message ['filename'] ), 'line' => (int) $message ['lineno'],
             'exception' => (string) ( $message ['exception'] ?? '' ), 'text' => trim ( (string) $message ) ];

  }

?>
