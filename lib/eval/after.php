<?php
  
  function pad_eval_set_type ( &$result, $k, $type ) {

    $result [$k] [0] = 'TYPE';
    $result [$k] [2] = $type;
    $result [$k] [5] = 0;

  }
   
  function pad_eval_after ( &$result ) {
 
    global $pad_flag_store, $pad_data_store, $pad_content_store;

    $check = 0;
    
    foreach($result as $one) {
      if ($one[1] == 'close')
        $check--;
      if ($check < 0)
        return pad_error ("Incorrect use of )");
      if ($one[1] == 'open')
        $check++;
    }

    if ($check <> 0)
      return pad_error ("Unequal () pairs");

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' and pad_valid_name ($one[0]) ) {
        $type = pad_get_type_eval ( $one[0] );
        if ( $pad_tag_type !== FALSE ) {
          $result[$k][0] = 'TYPE';
          $result[$k][1] = 'OPR';
          $result[$k][2] = $type;
          $result[$k][3] = $type;
          $result[$k][5] = 0;
        }
      }

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' {
        $exp = pad_explode ($one[0], ':');
        if ( count($exp) == 2 and pad_valid_name ($exp[1]) and file_exists ( PAD_HOME . "eval/" . $exp[0] . ".php" ) ) {
          $result[$k][0] = 'TYPE';
          $result[$k][1] = 'OPR';
          $result[$k][2] = $exp[1];
          $result[$k][3] = $exp[1];
          $result[$k][5] = 0;
        }
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
            $result[$k][6] = 'array';
            $result[$k][7] = constant ( $one[0] );
          }
          else
            $result[$k][0] = (string) constant ( $one[0] );
 
        }

    foreach ($result as $k => $one)
      if ( $one[1] == 'other' )
        $result[$k][1] = 'VAL';

  }
  
?>