<?php

  
  function padEvalAfter ( &$result, $eval ) {
 
    global $padFlagStore, $padDataStore, $padContentStore;

    $check = 0;
    
    foreach($result as $one) {
      if ($one[1] == 'close')
        $check--;
      if ($check < 0)
        padError ( "Incorrect use of ): $eval" );
      if ($one[1] == 'open')
        $check++;
    }

    if ($check <> 0) 
      padError ("Unequal () pairs: $eval" );

    foreach ($result as $k => $one)
      if ( $one[1] == 'hex' ) {
        $result[$k][0] = hex2bin($one[0]);
        $result[$k][1] = 'VAL';
      }

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        $exp = padExplode ($one[0], ':');

        if ( count($exp) == 2 ) {
          $type = $exp[0];
          $val  = $exp[1];
        }
        else {
          $type = padGetTypeEval ( $one[0] );
          $val  = $one[0];
        }

        if ( padValid ($type) and padValid ($val) ) {

          if ( file_exists ( PAD . "pad/eval/single/$type.php") or file_exists ( PAD . "pad/eval/parms/$type.php" ) ) {
            $result[$k][0] = $val;
            $result[$k][1] = 'TYPE';
            $result[$k][2] = $type;          
            $result[$k][3] = 0;
          }

          if ( file_exists ( PAD . "pad/eval/single/$type.php" ) )
            padEvalSingle ( $result, $k );

        }

      }

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        if ( isset ( padEval_alt [$one[0]] ) ) {
          $result[$k][0] = padEval_alt [$one[0]];
          $result[$k][1] = 'OPR';
        }

        if ( in_array ( strtoupper($one[0]), padEval_txt ) ) {
          $result[$k][0] = strtoupper($one[0]);
          $result[$k][1] = 'OPR';
        }

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

        $result[$k][1] = 'VAL';      
 
        if ( padFieldCheck ( $one[0] ) ) 
          $result[$k][0] = padFieldValue ( $one[0] );
        elseif ( padArrayCheck ( $one[0] ) ) {
          $result[$k][0] = '*ARRAY*';
          $result[$k][4] = padArrayValue ( $one[0] );
        } else
          padError ( 'Unknow $variable: ' . $one[0] );
 
      }

    }

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' )
        padError ( 'Unknow eval argument: ' . $one[0] );
 
  }

  
?>