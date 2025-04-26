<?php

  if ( count ( $pqActionList ) == 1 )

    array_splice (
      $pqResult,
      $pqActionList [0]
    );

  elseif ( count ( $pqActionList ) == 2 ) {

    if ( is_numeric ( $pqActionList [1] ) )

      array_splice (
        $pqResult,
        $pqActionList [0],
        $pqActionList [1]
      );
    
    else 
    
      array_splice (
        $pqResult,
        $pqActionList [0],
        null,
        $pqStore [ $pqActionList [1] ]
      );

  }

  elseif ( count ( $pqActionList ) == 3 )
    array_splice (
      $pqResult,
      $pqActionList [0],
      $pqActionList [1],
      $pqStore [ $pqActionList [2] ]
    );

  return $pqResult;

?>