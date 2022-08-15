 <?php
 
  function padEvalType ($type, $left, &$result, $myself, $start, $end=999999) {

    $kind = $result [$type] [2];
    $name = $result [$type] [0];

    if ( file_exists ( PAD."eval/single/$kind.php" ) ) {
      padEvalSingle ( $result, $type);
      return;
    }

    $padarm = [];

    foreach ( $result as $k => $v )
      if ($k <= $end and $k > $type and $k <= $result [$type] [3] - 1)
        if ( isset($v[4]) )
          foreach ($v[4] as $v2)
            $padarm [] = $v2;
        else
          $padarm [] = $v[0];

    if ( ! count($padarm) )
      foreach ( $result as $k => $v )
        if ($k <= $end and  $k > $type ) {
          if ( isset($v[4]) ) {
            $padarm = array_values($v[4]);
            unset($result[$k]);
          }
          break;
        }

    $count = count($padarm);

    if ( $left >= $start and isset($result [$left] [4]) ) {
      $value = array_values($result [$left] [4]);
      unset($result [$left]);
    } elseif ( $left >= $start and $result [$left] [1] == 'VAL') {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
   
    if ( $GLOBALS ['padTrace'] ) {
        $trace_data ['type']   = $result [$type];
        $trace_data ['left']   = $left;
        $trace_data ['start']  = $start;
        $trace_data ['parm']   = $padarm;
        $trace_data ['in']     = $value;
    }
   
    $value = include PAD . "eval/parms/$kind.php" ;
    
    if ( $GLOBALS ['padTrace'] )
      $trace_data ['out'] = $value;

    $result [$type] [1] = 'VAL';
  
    if ( is_array($value) or is_object($value) or is_resource($value) ) {
      $result [$type] [0] = '*ARRAY*';
      $result [$type] [4] = padArraySingle ($value);
     } else {
      padCheckValue ($value);
      $result [$type] [0] = $value;
    }

    foreach ( $result as $key => $padarm)
      if ( $key <= $end and $key > $type and $key <= $result [$type] [3] - 1 )
        unset($result[$key]);

    if ( $result [$type] [1] == 'VAL' ) {
      unset ( $result [$type] [2] );
      unset ( $result [$type] [3] );
    }

    if ( $GLOBALS ['padTrace'] ) {
      $trace_data ['result'] = $result [$type];
      padEvalTrace  ('type', $trace_data );
    }

  }

  function padArraySingle ($value) {

    $value = padToArray($value);

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