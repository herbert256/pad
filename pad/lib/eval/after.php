<?php
  
  function pad_eval_after ( &$result ) {
 
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
      
      if ( $one[1] == 'other' )  {

        $option = $one[0];

        $php = '';
        if ( pad_valid_name($option) )
          if ( file_exists(PAD_APP . "functions/$option.php") )
            $php = PAD_APP . "functions/$option.php";
          elseif ( file_exists(PAD_HOME . "functions/$option.php") )
            $php = PAD_HOME . "functions/$option.php";

        if ($php) {
          $result[$k][0] = 'PHP';
          $result[$k][1] = 'OPR';
          $result[$k][2] = $php;
          $result[$k][3] = $option;
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