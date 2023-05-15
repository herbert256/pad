<?php


   function padEvalSingle ( &$result, $key) {
    
    $one = $result [$key];

    $name  = $one[0];
    $kind  = $one[2];
    $parm  = [];
    $count = 0;
    
    $padEvalSingle = include pad . "eval/single/$kind.php"; 

    $result [$key] [1] = 'VAL';

    if ( is_array($padEvalSingle) or is_object($padEvalSingle) or is_resource($padEvalSingle) ) {
      $result [$key] [0] = '*ARRAY*';
      $result [$key] [4] = padArraySingle ($padEvalSingle);
    } else {
      padCheckValue ($padEvalSingle);
      $result [$key] [0] = $padEvalSingle;
    }

    unset ( $result [$key] [2] );
    unset ( $result [$key] [3] );

  }


  function padArraySingle ($value) {

    $value = padToArray ($value);

    $array = [];

    foreach ( $value as $v1 )
      if ( ! is_array($v1) )
        $array [] = $v1;
      else 
        foreach ( $v1 as $v2 ) {
          if ( ! is_array($v2) )
             $array [] = $v2;
          else 
            foreach ( $v2 as $v3 ) {
              if ( ! is_array($v3) )
                $array [] = $v3;
              break;
            }
          break;
        }

    return $array;

  }


?>