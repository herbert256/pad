<?php

  // Promotes the incoming request to globals, which is how a form field lands in a template
  // as {$fieldname} with no plumbing in between.
  //
  // POST, GET and cookie values are trimmed and copied into $GLOBALS, but only under names
  // that pass padValidVar() and only where the global does not exist yet - so an application
  // can protect a name by setting it in _inits.php first, and _config settings can never be
  // overwritten from the URL.
  //
  // If the application declared $padSessionVars the session is started as well, its values
  // are taken in the same way, and every declared variable is guaranteed to exist (empty
  // string if absent) so templates need not test for it.

  padGetParms ('POST',   $_POST  );
  padGetParms ('GET',    $_GET   );
  padGetParms ('COOKIE', $_COOKIE);

  if (count($padSessionVars) ) {

    if ( ! ini_get('session.auto_start') )
      session_start();

    padGetParms ('SESSION', $_SESSION);

    foreach ($padSessionVars as $padVar)
      if ( ! isset ($GLOBALS [$padVar]) )
        $GLOBALS [$padVar] = '';

    $padSessionStarted = TRUE;

  }

?>