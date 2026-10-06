<?php

  // Name and path validators. They are the engine's gatekeepers: a name that fails here
  // is not treated as a tag, type or variable, and a path that fails is never opened.
  //
  // padValid      a general tag/type name, letters then letters, digits, _ : # - or any
  //               name carrying an @ property (padAtCheck), as in first@items
  // padValidTag   the tag form of the same test
  // padValidVar   an application variable: an identifier starting with a letter, and never
  //               an engine name (padEngineName), so request input and templates cannot
  //               overwrite engine state - nor $this, which PHP refuses to assign: ?this=1
  //               ended the request with a 500
  // padEngineName pad*, pq*, PHP's superglobals, GLOBALS and this - the names that belong
  //               to the engine or to PHP
  // padAtValid    one part either side of an @ in a property reference
  // padValidName  a name the engine joins into a file path or a glob - a prefixed type
  //               name, a script, callback or option: no directory separator, no .., no
  //               glob character and no NUL, so it can only ever name something inside the
  //               directory it is looked up in
  // padValidID    a session or request id as padRandomString() mints it, eight letters
  //               and digits

  function padValid ( $name ) {

    if ( trim ( $name ) == '' )
      return FALSE;

    if ( padAtCheck ( $name ) )
      return TRUE;

    if ( ! preg_match ( '/^[a-zA-Z][:#a-zA-Z0-9_]*$/',$name ) )
      return FALSE;

    return TRUE;

  }

  function padValidName ( $name ) {

    if ( ! is_scalar ( $name ) )               return FALSE;
    if ( trim ( $name ) == '' )                return FALSE;
    if ( strpbrk ( $name, "/\\*?[\0" ) !== FALSE ) return FALSE;
    if ( str_contains ( $name, '..' ) )        return FALSE;

    return TRUE;

  }

  function padValidID ( $id ) {

    return is_string ( $id ) and preg_match ( '/^[A-Za-z0-9]{8}$/', $id );

  }

  // A name starting with _ is refused too: it is the shape of PHP's own superglobals, and
  // ?_SESSION[x]=1 was promoted like any other name.

  function padValidVar ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/',$name) )   return FALSE;
    if ( padEngineName ( $name ) )                           return FALSE;

    return TRUE;

  }

  // The one line between the application's names and the engine's, shared by padValidVar
  // and padValidStore: pad* (the engine's state), pq* (the sequence subsystem's - the
  // request could preset it), PHP's superglobals and GLOBALS, and this.

  function padEngineName ( $name ) {

    return str_starts_with ( $name, 'pad' ) or str_starts_with ( $name, 'pq' )
        or in_array ( $name, [ 'GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION', '_FILES',
                               '_SERVER', '_REQUEST', '_ENV', 'this' ], TRUE );

  }

  function padAtValid ( $part ) {

    if ( trim($part) == '' )                                       return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z0-9_-][a-zA-Z0-9_:]*$/', $part ) ) return FALSE;

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
