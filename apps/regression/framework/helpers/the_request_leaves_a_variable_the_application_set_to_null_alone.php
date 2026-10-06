<?php

  // A variable the application declared NULL - $currentUser = NULL in _config/config.php,
  // filled by _inits.php only for a signed-in visitor - is set, and a request value of the
  // same name leaves it alone, as it leaves a '' alone.

  $GLOBALS ['zzDeclaredNull']  = NULL;
  $GLOBALS ['zzDeclaredEmpty'] = '';

  padGetParms ( 'GET', [ 'zzDeclaredNull' => 'admin', 'zzDeclaredEmpty' => 'admin', 'zzNotDeclared' => 'given' ] );

  $result = var_export ( $GLOBALS ['zzDeclaredNull'], TRUE ) . ' ' . var_export ( $GLOBALS ['zzDeclaredEmpty'], TRUE )
          . ' ' . var_export ( $GLOBALS ['zzNotDeclared'] ?? NULL, TRUE );

?>
