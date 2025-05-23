<?php

  $pqAction     = $pqSetAction;
  $pqActionParm = '';
  $pqParms      = [];
  $pqFirst      = pqActionArray ( $pqSetParms );
  
   if ( is_array ( $pqFirst )  ) {

    $pqBuild = 'given';
    $pqFixed = $pqFirst;
    
   } else {

    $pqBuild = 'pull';
    $pqPull  = $pqFirst;

  }

  foreach ( $pqSetParms as $pqV )

    if ( is_array ( $pqV ) ) {

      $pqT            = "__action__" . padRandomString ();
      $pqStore [$pqT] = array_values ( $pqV );
      $pqActionParm  .= $pqT . '|';
    
    } else

      $pqActionParm .= $pqV . '|';

?>