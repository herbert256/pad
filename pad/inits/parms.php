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
  // If the application declared $padSessionVars the session is started and taken in
  // first, so the state the server keeps outranks anything the request says; every
  // declared variable is guaranteed to exist (empty string if absent) so templates need
  // not test for it. Then POST, GET and cookie values, trimmed, for the names
  // $padRequestVars lets through - never a declared session name, which a request could
  // otherwise set for the session to keep.

  if (count($padSessionVars) ) {

    // Strict mode: a session id the server never issued is refused and a fresh one made,
    // so a visitor cannot plant an id of their choosing. The cookie is kept from scripts
    // and cross-site requests, and travels only over https when the page did.

    if ( session_status () !== PHP_SESSION_ACTIVE ) {

      ini_set ( 'session.use_strict_mode', '1' );

      session_set_cookie_params ( [
        'httponly' => TRUE,
        'samesite' => 'Lax',
        'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
      ] );

      session_start();

    }

    padGetParms ('SESSION', $_SESSION);

    foreach ($padSessionVars as $padVar)
      if ( ! isset ($GLOBALS [$padVar]) )
        $GLOBALS [$padVar] = '';

    $padSessionStarted = TRUE;

  }

  padGetParms ('POST',   $_POST  );
  padGetParms ('GET',    $_GET   );
  padGetParms ('COOKIE', $_COOKIE);

?>