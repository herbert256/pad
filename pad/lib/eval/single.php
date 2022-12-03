<?php


   function padEvalSingle ( &$result, $key) {
    
    if ( $GLOBALS ['padTrace'] )
      $trace_data ['before'] = $result[$key];

    $one = $result [$key];

    $name  = $one[0];
    $kind  = $one[2];
    $parm  = [];
    $count = 0;
    
    $padEvalSingle = include PAD . "eval/single/$kind.php"; 

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

    if ( $GLOBALS ['padTrace'] ) {
      $trace_data ['after'] = $result [$key];
      padEvalTrace ('single', $trace_data );
    }   

  }

  
?>