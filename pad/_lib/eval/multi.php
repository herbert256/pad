<?php


  function padEvalMulti( &$result ) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) { 

      if ( $previous !== NULL ) 

        if ( $result [$now] [1] == 'VAL' and $result [$previous] [1] == 'VAL' ) {
        
          $result [$now] [0] = $result [$previous] [0] . $result [$now] [0];
        
          unset ( $result [$previous] );
        
        } 

      $previous = $now;

    }

  }


?>