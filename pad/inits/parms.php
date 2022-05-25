<?php


  pad_get_vars ();


  function pad_get_vars () {

    global $pad_session_vars;
    
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
  
      $GLOBALS['pad_session_started'] = TRUE;
        
    }
  
  }


  function pad_get_parms ( $type, $parms ) {

    foreach ( $parms as $field => $value )
      if ( (!isset($GLOBALS[$field])) )
        $GLOBALS [$field] = pad_get_parms_2 ( $type, $value );

  }


  function pad_get_parms_2 ( $type, $field ) {

    if ( is_array ( $field ) )
      foreach ( $field as $key => $value )
        $field [$key] = pad_get_parms_2 ( $type, $value );
    else
      $field = trim ($field);

    return $field;

  }

?>