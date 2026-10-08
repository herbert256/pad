<?php

  // Authorization: what the user logged in (lib/auth.php) may do - Laravel's Gate and @can,
  // Pundit's and CanCan's policies, Django's permissions - as abilities with a name, each
  // decided by a function the application defines once, in a file of its _lib/:
  //
  //   padGate       ( 'edit-post', fn ( $user, $post ) => $user ['id'] == $post ['user_id'] );
  //   padGateBefore ( fn ( $user, $ability ) => $user ['role'] == 'admin' ? TRUE : NULL );
  //
  // padGate        defines an ability: its name and the function that decides it, given
  //                the user's row and whatever the check passes along
  // padGateBefore  a function asked before every ability, given the user and the ability's
  //                name (and the values): TRUE or FALSE decides, NULL leaves it to the gate
  // padCan         whether the user may - padCan ( 'edit-post', $post )
  // padCannot      the opposite
  // padAuthorize   lets the request on when the user may, ends it with 403 when not
  //
  // padGateList / padGateGuests / padGateValue are the private helpers below them.
  //
  // A guest - nobody logged in - may do nothing, and no gate is even asked, unless the
  // function says it takes a guest: its first parameter allows NULL, ?array $user or
  // $user = NULL, as Laravel's gates do. An ability nobody defined is FALSE, and under the
  // strict check an error: a misspelled name in a template denied silently. The template
  // asks with {can 'edit-post', $post}...{else}...{/can} and {cannot ...}.
  //
  // The definitions live in a static of padGateList, not in a global - an error report
  // shows globals, and a Closure in one is no data. They are made per request, by _lib.

  function padGate ( $ability, $callback ) {

    if ( ! is_string ( $ability ) or ! preg_match ( '/^[A-Za-z0-9_][A-Za-z0-9_.:-]*$/D', $ability ) ) {
      padError ( 'padGate: an ability is a name of letters, digits, _ . : and -, not ' . padRequestShow ( $ability ) );
      return FALSE;
    }

    if ( ! is_callable ( $callback ) ) {
      padError ( "padGate: the ability '$ability' needs a function that decides it, not " . padRequestShow ( $callback ) );
      return FALSE;
    }

    $list = &padGateList ();
    $list ['gates'] [$ability] = $callback;

    return TRUE;

  }

  function padGateBefore ( $callback ) {

    if ( ! is_callable ( $callback ) ) {
      padError ( 'padGateBefore: a function is needed, not ' . padRequestShow ( $callback ) );
      return FALSE;
    }

    $list = &padGateList ();
    $list ['before'] [] = $callback;

    return TRUE;

  }

  function padCan ( $ability, ...$values ) {

    global $padCheckSyntax;

    $list = padGateList ();

    if ( ! is_string ( $ability ) or ! isset ( $list ['gates'] [$ability] ) ) {
      if ( $padCheckSyntax )
        padError ( 'padCan: there is no ability named ' . padRequestShow ( $ability ) . " - padGate defines one" );
      return FALSE;
    }

    $user = padUser ();

    foreach ( $list ['before'] ?? [] as $before ) {

      if ( $user === NULL and ! padGateGuests ( $before ) )
        continue;

      $answer = $before ( $user, $ability, ...$values );

      if ( $answer !== NULL )
        return (bool) $answer;

    }

    $gate = $list ['gates'] [$ability];

    if ( $user === NULL and ! padGateGuests ( $gate ) )
      return FALSE;

    return (bool) $gate ( $user, ...$values );

  }

  function padCannot ( $ability, ...$values ) {

    return ! padCan ( $ability, ...$values );

  }

  // A page, or a post, that is not for this user: 403 through padAbort, before anything of
  // the page is sent.

  function padAuthorize ( $ability, ...$values ) {

    if ( padCan ( $ability, ...$values ) )
      return TRUE;

    padAbort ( 403 );

  }

  // ------------------------------------------------------------------------------------
  // The private helpers.
  // ------------------------------------------------------------------------------------

  function &padGateList () {

    static $list = [ 'gates' => [], 'before' => [] ];

    return $list;

  }

  // Whether a function takes a guest: its first parameter allows NULL by its type or has
  // NULL for its default. A parameter without a type does not say so.

  function padGateGuests ( $callback ) {

    try {
      $parameters = ( new ReflectionFunction ( Closure::fromCallable ( $callback ) ) )->getParameters ();
    } catch ( Throwable $e ) {
      return FALSE;
    }

    if ( ! isset ( $parameters [0] ) )
      return FALSE;

    $first = $parameters [0];

    if ( $first->hasType () and $first->allowsNull () )
      return TRUE;

    return $first->isDefaultValueAvailable () and $first->getDefaultValue () === NULL;

  }

  // One value {can} hands its gate: $posts inside {posts} is the row of this occurrence - a
  // gate decides on one post, not on the list - another $name of an array is that array,
  // anything else an expression (padAttrsValue, lib/attrs.php).

  function padGateValue ( $expr ) {

    global $pad, $padCurrent, $padName;

    $expr = trim ( $expr );

    if ( preg_match ( '/^\$([A-Za-z_][A-Za-z0-9_]*)$/D', $expr, $match ) )
      for ( $i = $pad - 1; $i > 0; $i-- )
        if ( ( $padName [$i] ?? '' ) === $match [1] and is_array ( $padCurrent [$i] ?? NULL ) )
          return $padCurrent [$i];

    return padAttrsValue ( $expr );

  }

?>
