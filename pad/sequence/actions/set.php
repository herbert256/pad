<?php

  $pqAction = $pqSetAction;

  $pqFirst = pqActionArray ( $pqSetParms );
  
   if ( is_array ( $pqFirst )  ) {

    $pqBuild = 'given';
    $pqFixed = $pqFirst;
    
   } else {

    $pqBuild = 'pull';
    $pqPull  = $pqFirst;

  }

  if ( ! file_exists ( "sequence/actions/double/$pqAction")) 

    $pqActionParm = implode ( '|', $pqSetParms );

  else { 

    $pqSecond = pqActionArray ( $pqSetParms );
  
    if ( is_array ( $pqSecond ) ) {

      $pqActionParm = "__action__" . padRandomString ();
      $pqStore [$pqActionParm] = array_values ( $pqSecond );

      if ( count ( $pqSetParms ) )
        $pqActionParm .= '|' . implode ( '|', $pqSetParms );
    
    } else {

      $pqActionParm = implode ( '|', $pqSetParms );

    }

  } 

  $pqParms = [];

?> 