<?php

  // Every line of the application's text files that holds the query - plain text or a
  // regular expression, with or without case, whole words or not. At most 2000 hits.

  $app   = editApp ( editArg ( $body, 'app' ) );
  $query = editArg ( $body, 'query' );

  if ( $query === '' )
    editFail ( 'there is nothing to search for' );

  $pattern = empty ( $body ['regex'] ) ? preg_quote ( $query, '/' ) : str_replace ( '/', '\/', $query );

  if ( ! empty ( $body ['word'] ) )
    $pattern = "\\b(?:$pattern)\\b";

  $pattern = "/$pattern/u" . ( empty ( $body ['case'] ) ? 'i' : '' );

  if ( @preg_match ( $pattern, '' ) === FALSE )
    editFail ( 'that is not a regular expression PHP takes' );

  $hits = [];
  $more = FALSE;

  foreach ( editTree ( $app ) as $one ) {

    if ( $one ['dir'] or $one ['size'] > 2 * 1024 * 1024 )
      continue;

    $file = editPath ( $app, $one ['root'], $one ['path'] );
    $text = (string) @file_get_contents ( $file );

    if ( str_contains ( $text, "\0" ) or ! mb_check_encoding ( $text, 'UTF-8' ) or ! preg_match ( $pattern, $text ) )
      continue;

    foreach ( explode ( "\n", $text ) as $i => $line ) {

      if ( ! preg_match_all ( $pattern, $line, $all, PREG_OFFSET_CAPTURE ) )
        continue;

      foreach ( $all [0] as [ $match, $offset ] ) {

        if ( count ( $hits ) >= 2000 ) {
          $more = TRUE;
          break 3;
        }

        // The line as shown: cut to 200 characters around the match.

        $from  = max ( 0, $offset - 60 );
        $shown = ( $from ? '…' : '' ) . substr ( $line, $from, 200 );
        $start = $offset - $from + ( $from ? strlen ( '…' ) : 0 );

        $hits [] = [ 'root' => $one ['root'], 'path' => $one ['path'], 'line' => $i + 1,
                     'column' => mb_strlen ( substr ( $line, 0, $offset ) ) + 1, 'matchLength' => mb_strlen ( $match ),
                     'text' => mb_convert_encoding ( $shown, 'UTF-8', 'UTF-8' ),
                     'start' => mb_strlen ( substr ( $shown, 0, $start ) ), 'length' => mb_strlen ( $match ) ];

      }

    }

  }

  return [ 'hits' => $hits, 'more' => $more ];

?>
