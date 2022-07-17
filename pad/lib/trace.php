<?php

  function pad_trace_get_app_vars () { return pad_trace_get_xxx_vars ('app'); }
  function pad_trace_get_pad_vars () { return pad_trace_get_xxx_vars ('pad'); }
  function pad_trace_get_php_vars () { return pad_trace_get_xxx_vars ('php'); }

  function pad_trace_get_xxx_vars ($type) {

    $chk = ['_GET','_POST','_COOKIE','_FILES','_SESSION','_SERVER','_ENV','_REQUEST'];
    $not = ['app','page','PADSESSID','PADREFID','PADREQID'];

    $dump = [];

    foreach ($GLOBALS as $key => $value)
      if (    ( $type == 'app' and substr($key, 0, 3) <> 'pad' and ! in_array($key, $chk) and ! in_array($key, $not) ) 
           or ( $type == 'pad' and substr($key, 0, 3) == 'pad'                            )
           or ( $type == 'php' and in_array($key, $chk)                                   ) 
         )
        $dump [$key] = $value;

    return $dump;

  }

?>