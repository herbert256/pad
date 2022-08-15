<?php

  padGetParms ('POST',   $_POST  );
  padGetParms ('GET',    $_GET   );
  padGetParms ('COOKIE', $_COOKIE);

  if (count($padSession_vars) ) {

    if ( ! ini_get('session.auto_start') )
      session_start();

    padGetParms ('SESSION', $_SESSION);
    
    foreach ($padSession_vars as $padVar)
      if ( ! isset ($GLOBALS [$padVar]) )
        $GLOBALS [$padVar] = '';

    $padSession_started = TRUE;
      
  }
  
?>