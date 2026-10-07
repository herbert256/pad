<?php

  // The &name=value tail of a clean URL is read pair by pair: parse_str over the whole tail
  // warned past max_input_vars (1000), and index.php/page&a0=1&...&a1099=1 - any visitor's
  // URL, on a server that hands such a path to PHP (php -S, nginx) - answered 500 "Input
  // variables exceeded 1000", where the same pairs in a query string answer the page. Like
  // the query string, the tail takes as many pairs as max_input_vars allows and leaves the
  // rest. Asked here of padRequestPath directly: Apache maps a path that long to a file name
  // first and refuses it ("File name too long") before PHP is reached.

  $tpServer = $_SERVER;
  $tpGet    = $_GET;
  $tpReq    = $_REQUEST;

  $tpPairs = implode ( '&', array_map ( fn ( $i ) => "a$i=1", range ( 0, 1099 ) ) );

  $_SERVER ['PATH_INFO']   = "/request/tailcount&$tpPairs";
  $_SERVER ['REQUEST_URI'] = $_SERVER ['SCRIPT_NAME'] . $_SERVER ['PATH_INFO'];

  $tpPath   = padRequestPath ();
  $tpResult = $tpPath . ' a0=' . ( $_GET ['a0'] ?? 'none' ) . ' a1099=' . ( $_GET ['a1099'] ?? 'none' );

  $_SERVER  = $tpServer;
  $_GET     = $tpGet;
  $_REQUEST = $tpReq;

?>
