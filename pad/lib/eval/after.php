<?php


   function pad_eval_single ( &$result, $key) {
    
    if ( $GLOBALS ['pad_trace_eval'] )
      $trace_data ['before'] = $result[$key];

    $one = $result [$key];

    $name  = $one[0];
    $kind  = $one[2];
    $parm  = [];
    $count = 0;
    $pad_eval_single = include PAD . "eval/single/$kind.php"; 

    $result [$key] [1] = 'VAL';

    if ( is_array($pad_eval_single) or is_object($pad_eval_single) or is_resource($pad_eval_single) ) {
      $result [$key] [0] = '*ARRAY*';
      $result [$key] [4] = pad_array_single ($pad_eval_single);
    } else {
      pad_check_value ($pad_eval_single);
      $result [$key] [0] = $pad_eval_single;
    }

    unset ( $result [$key] [2] );
    unset ( $result [$key] [3] );

    if ( $GLOBALS ['pad_trace_eval'] ) {
      $trace_data ['after'] = $result [$key];
      pad_eval_trace ('single', $trace_data );
    }   

  }

  
  function pad_eval_after ( &$result, $eval ) {
 
    global $pad_flag_store, $pad_data_store, $pad_content_store;

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
      if ( $one[1] == 'other' and pad_valid ($one[0]) ) {
        $type = pad_get_type_eval ( $one[0] );
        if ( $type !== FALSE ) {
          $result[$k][0] = $one[0];
          $result[$k][1] = 'TYPE';
          $result[$k][2] = $type;
          $result[$k][3] = 0;
        }
      }

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' ) {
        $exp = pad_explode ($one[0], ':');
        if ( count($exp) == 2 and pad_valid ($exp[0]) and pad_valid ($exp[1]) ) {
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
      if ( $one[1] == 'TYPE' and pad_valid ($one[2]) and file_exists ( PAD."eval/single/".$one[2].".php" ) )
        pad_eval_single ( $result, $key);

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and isset ( pad_eval_alt [$one[0]] ) ) {
          $result[$k][0] = pad_eval_alt [$one[0]];
          $result[$k][1] = 'OPR';
      } 

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and in_array ( strtoupper($one[0]), pad_eval_txt ) ) {
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
 
        if ( pad_field_check ( $one[0] ) ) 
          $result[$k][0] = pad_field_value ( $one[0] );
        elseif ( pad_array_check ( $one[0] ) ) {
          $result[$k][0] = '*ARRAY*';
          $result[$k][4] = pad_array_value ( $one[0] );
        } else
          $result[$k][0] = $one[0]   ;

        if ( $GLOBALS ['pad_trace_eval'] ) {
          if ( isset($result[$k][4]) )
            $trace_data ['value'] = $result[$k][4];
          else
            $trace_data ['value'] = $result[$k][0];
          pad_eval_trace ('var', $trace_data );
        }  

      }

    }


    foreach ($result as $k => $one)
      if ( $one[1] == 'other' )
        return 'Unknow eval argument: ' . $one[0];
 
    return '';

  }
  
?>