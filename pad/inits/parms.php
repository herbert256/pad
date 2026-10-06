<?php

  // Promotes the incoming request to globals, which is how a form field lands in a template
  // as {$fieldname} with no plumbing in between.
  //
  // Values are copied into $GLOBALS only under names that pass padValidVar() and only
  // where the global does not exist yet, so the first source wins and _config settings can
  // never be overwritten from the URL. The application's own _inits.php and page .php run
  // after this - an assignment there replaces a request value, but code that merely tests
  // whether a name is set sees the one the request brought.
  //
  // If the application declared $padSessionVars the session is started and those names
  // are taken in first, so the state the server keeps outranks anything the request says; every
  // declared variable is guaranteed to exist (empty string if absent) so templates need
  // not test for it. Then POST, GET and cookie values, trimmed, for the names
  // $padRequestVars lets through - never a declared session name, which a request could
  // otherwise set for the session to keep.

  if (count($padSessionVars) ) {

    // A segment of a clean URL was bound before the configuration said which names are the
    // session's (inits/page.php, lib/route.php); such a name is the session's, not the path's.

    foreach ( $padSessionVars as $padVar )
      if ( array_key_exists ( $padVar, $padRouteVars ?? [] ) )
        unset ( $GLOBALS [$padVar] );

    padSessionStart ();

    // The declared names only: the session cookie is the host's, so every key another
    // application kept in it - or a helper keeping its own state - came out as a variable
    // of this one, ahead of the request and the page, once any name was declared.

    padGetParms ('SESSION', array_intersect_key ( $_SESSION ?? [], array_flip ( $padSessionVars ) ) );

    foreach ($padSessionVars as $padVar)
      if ( ! isset ($GLOBALS [$padVar]) )
        $GLOBALS [$padVar] = '';

    $padSessionStarted = TRUE;

  }

  padGetParms ('POST',   $_POST  );
  padGetParms ('GET',    $_GET   );
  padGetParms ('COOKIE', $_COOKIE);

?>
