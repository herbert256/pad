<?php

  // The user request/rememberform finds for a remember-me cookie ($padAuthRemember, set for
  // that page in _config/config.php).

  function pagesRememberFind ( $id ) {

    return ( (string) $id === '7' ) ? [ 'id' => 7, 'name' => 'Ann', 'password' => 'fixed-hash-of-ann' ] : NULL;

  }

?>
