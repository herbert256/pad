<?php

  // A $padSessionVars name the configuration declares NULL - $rvUser = NULL, the signed-out
  // default - is filled from the session on the next request: the session is the
  // application's own state. A request value still leaves such a NULL alone.

  $GLOBALS ['zzSessUser'] = NULL;
  $GLOBALS ['zzReqUser']  = NULL;

  padGetParms ( 'SESSION', [ 'zzSessUser' => 'ann' ] );
  padGetParms ( 'GET',     [ 'zzReqUser'  => 'admin' ] );

  $result = var_export ( $GLOBALS ['zzSessUser'], TRUE ) . ' ' . var_export ( $GLOBALS ['zzReqUser'], TRUE );

?>
