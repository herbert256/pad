<?php

  pad_get_parms ('POST',   $_POST  );
  pad_get_parms ('GET',    $_GET   );
  pad_get_parms ('COOKIE', $_COOKIE);

  if (count($pad_session_vars) ) {

    if ( ! ini_get('session.auto_start') )
      session_start();

    pad_get_parms ('SESSION', $_SESSION);
    
    foreach ($pad_session_vars as $pad_var)
      if ( ! isset ($GLOBALS [$pad_var]) )
        $GLOBALS [$pad_var] = '';

    $pad_session_started = TRUE;
      
  }
  
?>