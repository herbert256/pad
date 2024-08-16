<?php

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