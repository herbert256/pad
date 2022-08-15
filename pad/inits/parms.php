<?php

  pGet_parms ('POST',   $_POST  );
  pGet_parms ('GET',    $_GET   );
  pGet_parms ('COOKIE', $_COOKIE);

  if (count($padSession_vars) ) {

    if ( ! ini_get('session.auto_start') )
      session_start();

    pGet_parms ('SESSION', $_SESSION);
    
    foreach ($padSession_vars as $padVar)
      if ( ! isset ($GLOBALS [$padVar]) )
        $GLOBALS [$padVar] = '';

    $padSession_started = TRUE;
      
  }
  
?>