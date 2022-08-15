<?php


   function padEvalSingle ( &$result, $key) {
    
    if ( $GLOBALS ['padTrace'] )
      $trace_data ['before'] = $result[$key];

    $one = $result [$key];

    $name  = $one[0];
    $kind  = $one[2];
    $padarm  = [];
    $count = 0;
    $padEval_single = include PAD . "eval/single/$kind.php"; 

    $result [$key] [1] = 'VAL';

    if ( is_array($padEval_single) or is_object($padEval_single) or is_resource($padEval_single) ) {
      $result [$key] [0] = '*ARRAY*';
      $result [$key] [4] = padArraySingle ($padEval_single);
    } else {
      padCheckValue ($padEval_single);
      $result [$key] [0] = $padEval_single;
    }

    unset ( $result [$key] [2] );
    unset ( $result [$key] [3] );

    if ( $GLOBALS ['padTrace'] ) {
      $trace_data ['after'] = $result [$key];
      padEvalTrace ('single', $trace_data );
    }   

  }

  
  function padEvalAfter ( &$result, $eval ) {
 
    global $padFlagStore, $padDataStore, $padContentStore;

    $check = 0;
    
    foreach($result as $one) {
      if ($one[1] == 'close')
        $check--;
      if ($check < 0)
        return "Incorrect use of ): $eval";
      if ($one[1] == 'open')
        $check++;
    }

    if ($check <> 0) 
      return "Unequal () pairs: $eval";

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and padValid ($one[0]) ) {
        $type = padGetTypeEval ( $one[0] );
        if ( $type !== FALSE ) {
          $result[$k][0] = $one[0];
          $result[$k][1] = 'TYPE';
          $result[$k][2] = $type;
          $result[$k][3] = 0;
        }
      }

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' ) {
        $exp = padExplode ($one[0], ':');
        if ( count($exp) == 2 and padValid ($exp[0]) and padValid ($exp[1]) ) {
          $type = $exp[0];
          if ( file_exists ( PAD . "eval/single/$type.php") or file_exists ( PAD . "eval/parms/$type.php") ) {
            $result[$k][0] = $exp[1];
            $result[$k][1] = 'TYPE';
            $result[$k][2] = $type;          
            $result[$k][3] = 0;
          }
        }
      }

    foreach ($result as $key => $one)
      if ( $one[1] == 'TYPE' and padValid ($one[2]) and file_exists ( PAD."eval/single/".$one[2].".php" ) )
        padEvalSingle ( $result, $key);

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and isset ( padEval_alt [$one[0]] ) ) {
          $result[$k][0] = padEval_alt [$one[0]];
          $result[$k][1] = 'OPR';
      } 

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and in_array ( strtoupper($one[0]), padEval_txt ) ) {
          $result[$k][0] = strtoupper($one[0]);
          $result[$k][1] = 'OPR';
      } 

    foreach ($result as $k => $one)
      if ( $one[1] == 'hex' ) {
        $result[$k][0] = hex2bin($one[0]);
        $result[$k][1] = 'VAL';
      }

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' and defined ( $one[0] ) ) {

          $result[$k][1] = 'VAL';
        
          if ( is_array ( constant ( $one[0] ) )) {
            $result[$k][0] = '*ARRAY*';
            $result[$k][4] = constant ( $one[0] );
          }
          else
            $result[$k][0] = constant ( $one[0] );
 
        }


    foreach ( $result as $k => $one ) {

      if ( $one[1] == '$' ) {

        $trace_data ['index'] = $k;
        $trace_data ['field'] = $one[0];

        $result[$k][1] = 'VAL';      
 
        if ( padFieldCheck ( $one[0] ) ) 
          $result[$k][0] = padFieldValue ( $one[0] );
        elseif ( padArrayCheck ( $one[0] ) ) {
          $result[$k][0] = '*ARRAY*';
          $result[$k][4] = padArrayValue ( $one[0] );
        } else
          $result[$k][0] = $one[0]   ;

        if ( $GLOBALS ['padTrace'] ) {
          if ( isset($result[$k][4]) )
            $trace_data ['value'] = $result[$k][4];
          else
            $trace_data ['value'] = $result[$k][0];
          padEvalTrace ('var', $trace_data );
        }  

      }

    }


    foreach ($result as $k => $one)
      if ( $one[1] == 'other' )
        return 'Unknow eval argument: ' . $one[0];
 
    return '';

  }
  
?>