<?php

  // Name and path validators. They are the engine's gatekeepers: a name that fails here
  // is not treated as a tag, type or variable, and a path that fails is never opened.
  //
  // padValid      a general tag/type name, letters then letters, digits, _ : # - or any
  //               name carrying an @ property (padAtCheck), as in first@items
  // padValidTag   the tag form of the same test
  // padValidType  a bare type name, letters only
  // padValidVar   an application variable: identifier, and never a pad-prefixed name, so
  //               request input and templates cannot overwrite engine state
  // padAtValid    one part either side of an @ in a property reference
  // padValidFile  a file path: safe characters only, no .. or dot segments, and it must
  //               live under APP, DATA or PAD

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
    if ( str_starts_with($file, DATA)  ) return TRUE;
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