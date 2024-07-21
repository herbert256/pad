<?php


  function padData ( $input, $type='', $name='' ) {

    if     ( $input === NULL           ) $data = [];
    elseif ( $input === FALSE          ) $data = [];
    elseif ( $input === NAN            ) $data = [];
    elseif ( $input === INF            ) $data = [];
    elseif ( $input === TRUE           ) $data = [ 1 => [] ];
    elseif ( is_array ( $input)        ) $data = $input;
    elseif ( is_object ( $input)       ) $data = padToArray ( $input );
    elseif ( is_resource ( $input)     ) $data = padToArray ( $input );
    elseif ( ! $input                  ) $data = [];
    elseif ( strlen(trim($input)) == 0 ) $data = [];
    else                                 $data = trim ( $input );

    if ( ! is_array ( $data ) ) {    
      if ( ! $type )
        $type = padContentType ( $data );
      $data = include pad . "data/$type.php";
    }
    
    $data = padDataChkSimpleArray ($data,$name);
    $data = padDataChkChkOne      ($data,$name);   
    $data = padDataChkDataAttr    ($data,$name);
    $data = padDataChkCheckRecord ($data,$name); 
    $data = padDataChkCheckArray  ($data,$name);

    return $data;

  }
  

  function padDataChkSimpleArray ($data,$name) {

    $result = $data;

    foreach ($result as $padK => $padV)
      if ( is_array($padV) or ! is_numeric($padK) )
        return $result;
  
    $name   = padDataName($name);
    $tmp    = $result;
    $result = [];
    
    foreach ($tmp as $k => $v)
      $result [$k] [$name] = $v;

    return $result;

  }
  

  function padDataChkCheckArray ($data,$name) {

    $result = $data;

    foreach ($result as $k => $v) {
      $x = 0 ;
      foreach ($v as $k2 => $v2)
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          return $result;
    }

    $name = padDataName($name);

    foreach ($result as $k => $v) {
      $tmp = $v;
      $result [$k] = [];
      foreach ($tmp as $k2 => $v2)
        $result [$k] ["$name"] [$k2] ["$name"] = $v2;
    }

    return $result;

  }


  function padDataChkCheckRecord ($data,$name) {

    $result = $data;
    
    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return $result;
      }

    return $result;

  }


  function padDataChkChkOne ($data,$name) {

    $result = $data;

    if ( count($result) == 1 and is_array($result[array_key_first($result)]) ) {
      
      $idx=0;
      foreach ($result[array_key_first($result)] as $key => $value) {
        if ( $key <> $idx ) {
          $idx = 0;
          break;
        }
        $idx++;
      }
      
      if ($idx) {
        $tmp = $result[array_key_first($result)];
        $result = $tmp;
      }

    }
    
    return $result;

  }


  function padDataChkDataAttr ($data,$name) {

    $result = $data;
    
    foreach ($result as $k => $v)
      if ( is_array($v) )
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else
          $result [$k] = padDataChkDataAttr ( $result [$k], $name );

    return $result;

  }


  function padDataName ($name) {

    global $pad, $padPrm, $padTag, $padName, $padForceDataName;
    
    if     ( $name                              ) $return = $name;
    elseif ( isset ($padPrm [$pad] ['name'] )   ) $return = $padPrm [$pad] ['name'];
    elseif ( $padForceDataName                  ) $return = $padForceDataName;
    elseif ( isset ($padPrm [$pad] ['toData'] ) ) $return = $padPrm [$pad] ['toData'];
    elseif ( $padTag [$pad] == 'data '          ) $return = $padParm;
    elseif ( isset ($padTag [$pad] )            ) $return = $padTag [$pad];

    if ( substr($return, 0, 1) == '$' )
      $return = substr($return, 1);

    return $return;

  }
  

?>