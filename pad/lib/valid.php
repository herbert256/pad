<?php

  function padValid ( $name ) {

    if ( trim ( $name ) == '' )
      return FALSE;

    if ( padAtCheck ( $name ) !== INF )
      return TRUE;

    if ( ! preg_match ( '/^[a-zA-Z][:#a-zA-Z0-9_]*$/',$name ) )
      return FALSE;

    return TRUE;

  }

  function padValidFile ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, APP)  ) return TRUE;
    if ( str_starts_with($file, DAT)  ) return TRUE;
    if ( str_starts_with($file, PAD)  ) return TRUE;

    return FALSE;

  }

  function padValidVar ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( substr($name, 0, 3) == 'pad' )                      return FALSE;
    if ( ! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$name) )  return FALSE;

    return TRUE;

  }

  function padAtValid ( $part ) {

    if ( trim($part) == '' )                                       return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z0-9_-][a-zA-Z0-9_:]*$/', $part ) ) return FALSE;

    return TRUE;

  }

  function padValidType ($name) {

    if ( trim($name) == '' )
      return FALSE;

    if ( ! preg_match('/^[a-zA-Z][a-zA-Z]*$/',$name) )
      return FALSE;

    return TRUE;

  }

  function padValidTag ($name) {

    if ( trim($name) == '' )
      return FALSE;

    if ( padAtCheck ($name) )
      return TRUE;

    if ( preg_match('/^[a-zA-Z][a-zA-Z0-9:_]*$/',$name) )
      return TRUE;

    return FALSE;

  }

?>
