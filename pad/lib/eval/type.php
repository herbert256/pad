 <?php
 
  function pad_eval_type ($type, $left, &$result, $myself, $start, $end=999999) {

    $parm = [];

    foreach ( $result as $k => $v )
      if ($k <= $end and $k > $type and $k <= $result [$type] [5] - 1)
        if ( isset($v[6]) and $v[6] == 'array' )
          foreach ($v[7] as $v2)
            $parm [] = $v2;
        else
          $parm [] = $v[0];

    if ( ! count($parm) )
      foreach ( $result as $k => $v )
        if ($k <= $end and  $k > $type ) {
          if ( isset($v[6]) and $v[6] == 'array' ) {
            $parm = array_values($v[7]);
            unset($result[$k]);
          }
          break;
        }

    if ( $left >= $start and isset($result [$left] [6]) and $result [$left] [6] == 'array' ) {
      $value = array_values($result [$left] [7]);
      unset($result [$left]);
    } elseif ( $left >= $start and $result [$left] [1] == 'VAL') {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
  
    $kind  = $result [$type] [2];
    $name  = $result [$type] [3];
    $count = count($parm);
 
    if ( $GLOBALS ['pad_trace_eval'] ) {
        $trace_data ['kind']   = $kind;
        $trace_data ['name']   = $name;
        $trace_data ['left']   = $left;
        $trace_data ['start']  = $start;
        $trace_data ['myself'] = $myself;
        $trace_data ['in']     = $value;
        $trace_data ['out']    = '';
        $trace_data ['parm']   = $parm;
        $trace_data ['result'] = '';
        $trace_data ['before'] = $result;
    }
   
    $value = include PAD . "eval/$kind.php" ;
    
    if ( $GLOBALS ['pad_trace_eval'] )
      $trace_data ['value_out'] = $value;

    $result [$type] [1] = 'VAL';
  
    if ( is_array($value) or is_object($value) or is_resource($value) ) {
      $result [$type] [0] = '*ARRAY*';
      $result [$type] [6] = 'array';
      $result [$type] [7] = pad_array_single ($value);
     } else {
      pad_check_value ($value);
      $result [$type] [0] = $value;
    }

    foreach ( $result as $key => $parm)
      if ( $key <= $end and $key > $type and $key <= $result [$type] [5] - 1 )
        unset($result[$key]);

    if ( $result [$type] [1] == 'VAL' ) {
      unset ( $result [$type] [2] );
      unset ( $result [$type] [3] );
      unset ( $result [$type] [5] );
    }

    if ( $GLOBALS ['pad_trace_eval'] ) {
      $trace_data ['result'] = $result [$type];
      $trace_data ['after']  = $result;
      pad_eval_trace  ('type', $trace_data );
    }

  }

  function pad_array_single ($value) {

    $value = pad_xxx_to_array ($value);

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