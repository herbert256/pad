<?php

  function padClaudeCheck () {

    $addr  = $_SERVER ['REMOTE_ADDR']     ?? '';
    $agent = $_SERVER ['HTTP_USER_AGENT'] ?? '';

    if ( ! $addr or $addr == '::1' and str_contains($agent, 'curl') )
      return TRUE;
    else
      return FALSE;

  }

  function padClaudeError ( $error, $file, $line ) {

    if ( padClaudeCheck () ) {

      padClaudeFields ( $app, $pad, $php, $seq );

      $claude ['error']    = $error;
      $claude ['file']     = $file;
      $claude ['line']     = $line;
      $claude ['stack']    = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);
      $claude ['app']      = $app;
      $claude ['pad']      = $pad;
      $claude ['sequence'] = $seq;
      $claude ['php']      = $php;

      header ( 'HTTP/1.0 500 Internal Server Error' );
      header ( 'Content-Type: application/json' );

      echo json_encode ( $claude );

      exit;

    }

  }

  function padClaudeFields ( &$app, &$pad, &$php, &$seq ) {

    $seq = $php = $pad = $app = [];

    foreach ($GLOBALS as $key => $value)

      if ( substr($key, 0, 3)  == 'pad' )
        $pad [$key] = $value;
      elseif ( substr($key, 0, 2)  == 'pq' )
        $seq [$key] = $value;
      elseif ( substr($key, 0, 1)  == '_' )
        $php [$key] = $value;
      else
        $app [$key] = $value;

  }


?>