<?php

  function pad_include ( $app, $page='index', $query='' ) {

    return pad ( $app, $page, $query, 1 );
    
  }

  function pad ( $app, $page='index', $query='', $include='' ) {

    $result = pad_complete ( $app, $page, $query, $include );

    return $result ['data'];
    
  }

  function pad_complete ( $app, $page='index', $query='', $include='' ) {

    global $pad_host, $pad_script;

    if ($include)
      $include = '&pad_include=1';

    $input = $output = [];

    $input ['url'] = "$pad_host$pad_script?app=$app&page=$page$query$include";

    $input ['cookies'] ['PADSESSID'] = $GLOBALS['PADSESSID'];
    $input ['cookies'] ['PADREQID']  = $GLOBALS['PADREQID'];
    
    return pad_curl ($input);
    
  }
  
?>