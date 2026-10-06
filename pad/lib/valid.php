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
  // padEngineSecret an engine name that holds a credential - a password, a key, a token,
  //               the session id, the configuration's source text - which no field answers
  // padAtValid    one part either side of an @ in a property reference
  // padValidName  a name the engine joins into a file path or a glob - a prefixed type
  //               name, a script, callback or option: no directory separator, no .., no
  //               glob character and no NUL, so it can only ever name something inside the
  //               directory it is looked up in
  // padValidID    a session or request id as padRandomString() mints it, eight letters
  //               and digits
  // padPhpCallables  under a $padPhpFunctions list, the callables a PHP call is handed:
  //               every argument for a parameter the function declares callable must be
  //               a function the list allows (padPhpAllowed in lib/type.php)
  //
  // Every pattern ends in $/D: a bare $ also matches before a newline that ends the text,
  // so a cookie padSesID=abcdefgh%0A was an id PAD minted - and the newline went on into
  // the cookie header, where PHP refused it and the request ended in a 500 - and abc
  // followed by a newline was a variable name.

  function padValid ( $name ) {

    if ( trim ( $name ) == '' )
      return FALSE;

    if ( padAtCheck ( $name ) )
      return TRUE;

    if ( ! preg_match ( '/^[a-zA-Z][:#a-zA-Z0-9_]*$/D',$name ) )
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

    return is_string ( $id ) and preg_match ( '/^[A-Za-z0-9]{8}$/D', $id );

  }

  // A name starting with _ is refused too: it is the shape of PHP's own superglobals, and
  // ?_SESSION[x]=1 was promoted like any other name.

  function padValidVar ($name) {

    if ( trim($name) == '' )                                 return FALSE;
    if ( ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/D',$name) )  return FALSE;
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

  // The engine's credentials, which a template field never answers (lib/field/field.php):
  // the passwords of the databases, $padAppKey, the session id the cookie keeps from
  // scripts, and the source text of the configuration files that hold them. Named by the
  // rule the error reports redact by, so a credential added later is covered by its name.
  // The engine's other names stay fields - {$padHost}, {$padGo}, {$padPage} and the rest
  // that the applications' templates read.

  function padEngineSecret ( $name ) {

    if ( ! is_string ( $name ) or ! padEngineName ( $name ) )
      return FALSE;

    if ( in_array ( $name, [ 'padSesID', 'padConfigApp', 'padConfigDefault' ], TRUE ) )
      return TRUE;

    return (bool) preg_match ( '/pass(word|wd)?$|passwd|secret|token|api_?key|app_?key|private_?key/i', $name );

  }

  function padAtValid ( $part ) {

    if ( trim($part) == '' )                                        return FALSE;
    if ( ! preg_match ( '/^[a-zA-Z0-9_-][a-zA-Z0-9_:]*$/D', $part ) ) return FALSE;

    return TRUE;

  }

  // A $padPhpFunctions list names the functions a template may call, and a listed function
  // that takes a callable calls whatever it is handed: with array_map listed and strtoupper
  // not, php:array_map('strtoupper', $list) called strtoupper all the same, and
  // call_user_func or usort on the list opened every function. The parameters PHP declares
  // callable are held to the list; NULL, which array_map takes for no callback, passes. The
  // message for the first callable refused comes back, FALSE when there is none - always
  // FALSE under TRUE, which allows every function anyway.

  function padPhpCallables ( $name, $args ) {

    if ( ( $GLOBALS ['padPhpFunctions'] ?? TRUE ) === TRUE or ! function_exists ( $name ) )
      return FALSE;

    $params = ( new ReflectionFunction ( $name ) ) -> getParameters ();
    $last   = end ( $params );

    foreach ( array_values ( $args ) as $at => $arg ) {

      $param = $params [$at] ?? ( ( $last and $last -> isVariadic () ) ? $last : NULL );

      if ( ! $param or ! str_contains ( (string) $param -> getType (), 'callable' ) or $arg === NULL )
        continue;

      if ( ! is_string ( $arg ) )
        return "a callable that is no function name, handed to '$name', is not allowed by \$padPhpFunctions";

      if ( ! padPhpAllowed ( $arg ) )
        return "the PHP function '" . padMakeSafe ( $arg, 40 ) . "' is not allowed by \$padPhpFunctions";

    }

    return FALSE;

  }

  function padValidTag ($name) {

    if ( trim($name) == '' )
      return FALSE;

    if ( padAtCheck ($name) )
      return TRUE;

    if ( preg_match('/^[a-zA-Z][a-zA-Z0-9:_]*$/D',$name) )
      return TRUE;

    return FALSE;

  }

?>
