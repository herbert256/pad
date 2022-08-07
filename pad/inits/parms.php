<?php

  pGet_parms ('POST',   $_POST  );
  pGet_parms ('GET',    $_GET   );
  pGet_parms ('COOKIE', $_COOKIE);

  if (count($pSession_vars) ) {

    if ( ! ini_get('session.auto_start') )
      session_start();

    pGet_parms ('SESSION', $_SESSION);
    
    foreach ($pSession_vars as $pad_var)
      if ( ! isset ($GLOBALS [$pad_var]) )
        $GLOBALS [$pad_var] = '';

    $pSession_started = TRUE;
      
  }
  
?>