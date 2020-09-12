<?php


 function pad_data_chk (&$result) {

    if ( ! is_array ($result) )
      $result = [];

    pad_data_chk_simple_array ($result);
    pad_data_chk_chk_one      ($result);
    pad_data_chk_data_attr    ($result);
    pad_data_chk_check_record ($result);
    pad_data_chk_check_array  ($result);

  }


  function pad_data_chk_check_array (&$result) {
    
    if ( ! count($result) )
      return;

    foreach ($result as $k => $v) {

      $go = TRUE;
      $x = 0 ;

      foreach ($v as $k2 => $v2) {
        if ( ctype_digit( (string) $k2) and ( $k2 == $x or ($k2-1==$x) ) and ! is_array($v2) )
          $x++;
        else
          $go = FALSE;

      }

    }

    if ($go) {

      $name = pad_data_name();

      foreach ($result as $k => $v) {
        $tmp = $v;
        $result [$k] = [];
        foreach ($tmp as $k2 => $v2) {
          $result [$k] ["$name"] [$k2] ["$name"] = $v2;
        }
      }

    }

  }


  function pad_data_chk_check_record (&$result) {
    
    foreach ($result as $k => $v)
      if ( ! is_array($v) ) {
        $tmp = $result;
        $result = [];
        foreach ($tmp as $k => $v)
          $result [0] [$k] = $v;
        return;
      }

  }

  
  function pad_data_chk_simple_array (&$result) {

    $check = TRUE;
    foreach ($result as $pad_k => $pad_v)
      if ( is_array($pad_v) or ! is_numeric($pad_k) ) {
        $check = FALSE;
        break;
      }
  
    if ($check) {
      
      $name = pad_data_name();
      
      if (substr($name, 0, 1) == '$')
        $name = substr($name, 1);
  
      $tmp    = $result;
      $result = [];
      
      foreach ($tmp as $k => $v)
        $result [$k] [$name] = $v;
  
    }

  }


  function pad_data_chk_chk_one (&$result) {

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
    
  }


  function pad_data_chk_data_attr (&$result) {
    
    foreach ($result as $k => $v) {
      if ( is_array($v) ) {
        if (trim($k) == 'attr') {
          foreach ($v as $k2 => $v2)
            $result [$k2] = $v2;
          unset ($result [$k]);
        } else {
          pad_data_chk_data_attr ( $result [$k] );
        }
      }
    }

  }


  function pad_data_name () {

    if ( isset ($GLOBALS ['pad_data_name']) and $GLOBALS ['pad_data_name']) {
      $name = $GLOBALS ['pad_data_name'];
      unset ($GLOBALS ['pad_data_name']);
    }
    else
      $name = $GLOBALS['pad_name'];

    if ($name == 'data' and $GLOBALS['pad_tag_type'] == 'tag' and pad_valid_name($GLOBALS['pad_parm']) )
      return $GLOBALS['pad_parm'];

    return $name;

  }


?>