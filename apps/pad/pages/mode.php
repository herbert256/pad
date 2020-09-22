<?php

  function get_mode ($mode) {

    global $pad_host, $pad_script;

    $input = [];

    $input ['url'] = "$pad_host$pad_script?app=mode&page=level1/level2/page&mode=$mode";
    $input ['cache'] = FALSE;
    $input ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];

    pad_curl ($input, $output);

    return $output ['data'];
    
  }
  
?>