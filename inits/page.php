<?php

  if ( ! isset ( $_REQUEST['padPage'] ) and ! isset ( $_REQUEST['page'] ) )
    if ( $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE )
      if ( padPageCheck ($_SERVER['QUERY_STRING']) )
        $padPageSet = $_SERVER['QUERY_STRING'];

  if ( ! isset ($padPageSet) )
    if ( ! isset ( $_REQUEST['padPage'] ) and ! isset ( $_REQUEST['page'] ) ) {
      $padPageCheck = substr($_SERVER['QUERY_STRING'], 0, strpos($_SERVER['QUERY_STRING'], '&'));
      if ( padPageCheck ($padPageCheck) ) 
        $padPageSet = $padPageCheck;
    }

  $padPage = $padPage ?? $padPageSet ?? $_REQUEST['padPage'] ?? $_REQUEST['page'] ?? 'index';

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  
?>