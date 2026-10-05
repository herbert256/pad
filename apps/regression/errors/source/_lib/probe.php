<?php

  // Fetches a page of this directory in full - wrappers composed, which the suite's own
  // bare fetches never are - and returns what its error report says about the template:
  // 'where' the file, line and column, 'wrapped' the wrappers around it. A tool (curl)
  // gets the report as JSON, a browser as text; the caller picks which with $agent, so
  // both forms are held to it whoever runs the suite.

  function sourceProbe ( $page, $what, $agent ) {

    global $padHost;

    $body = padCurl ( [ 'url'     => $padHost . "regression/errors/?$page",
                        'options' => [ 'USERAGENT' => $agent ] ] ) ['data'] ?? '';
    $json = json_decode ( $body, TRUE );

    if ( is_array ( $json ) )
      $template = $json ['template'] ?? [];
    else {

      $text     = html_entity_decode ( $body, ENT_QUOTES, 'UTF-8' );
      $template = [];

      if ( preg_match ( '/(\S+)  line (\d+), column (\d+)/', $text, $m ) )
        $template = [ 'file' => $m [1], 'line' => $m [2], 'column' => $m [3] ];

      if ( preg_match ( '/wrapped by ([^\n<]+)/', $text, $m ) )
        $template ['wrapped'] = explode ( ' › ', trim ( $m [1] ) );

    }

    if ( $what == 'wrapped' )
      return implode ( ' › ', $template ['wrapped'] ?? [] );

    return ( $template ['file'] ?? '' ) . ':' . ( $template ['line'] ?? '' ) . ':' . ( $template ['column'] ?? '' );

  }

?>
