<?php

  
  function padEvalAfter ( &$result ) {
 
    global $padFlagStore, $padDataStore, $padContentStore;

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        $exp = padExplode ($one[0], ':');

        if ( count($exp) == 2 ) {
          $type = $exp[0];
          $name = $exp[1];
        }
        else {
          $type = padGetTypeEval ( $one[0] );
          $name = $one[0];
        }

        if ( padValid ($type) and padValid ($name) )

          if ( file_exists ( "/pad/eval/single/$type.php") ) {            

            if ( $GLOBALS ['padInfo'] ) 
              include '/pad/info/events/functions.php'; 

            $padCall = "/pad/eval/single/$type.php" ;
            $single  = include "/pad/call/any.php" ;

            $result [$k] [1] = 'VAL';
            $result [$k] [0] = padCheckValue ($single);

          } elseif ( file_exists ( "/pad/eval/parms/$type.php" ) ) {

            if ( $GLOBALS ['padInfo'] ) 
              include '/pad/info/events/functions.php'; 

            $result[$k][0] = $name;
            $result[$k][1] = 'TYPE';
            $result[$k][2] = $type;          
            $result[$k][3] = 0;
     
          }

      }

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        if ( isset ( padEval_alt [$one[0]] ) ) {

          $result[$k][0] = padEval_alt [$one[0]];
          $result[$k][1] = 'OPR';
        
        } elseif ( in_array ( strtoupper($one[0]), padEval_txt ) ) {
          
          $result[$k][0] = strtoupper($one[0]);
          $result[$k][1] = 'OPR';

        } else {

          $result[$k][1] = 'VAL';
          $result[$k][0] = constant ( $one[0] );

        }

      } elseif ( $one[1] == '$' ) {

        $result[$k][1] = 'VAL';   
        $result[$k][0] = padFieldValue ( $one[0] );
 
      } elseif ( $one[1] == '&' ) {

        $result[$k][1] = 'VAL';  
        $result[$k][0] = padTagValue ( $one[0], 1 );

      } elseif ( $one[1] == '#' ) {
 
        $result[$k][1] = 'VAL';  
        $result[$k][0] = padOptValue ( $one[0], 1 );
 
      } elseif ( $one[1] == 'hex' ) {

        $result[$k][1] = 'VAL';
        $result[$k][0] = hex2bin($one[0]);

      }

  }


?>