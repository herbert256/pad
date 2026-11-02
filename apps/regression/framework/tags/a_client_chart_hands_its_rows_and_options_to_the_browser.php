<?php

  // {chart:bar ..., client/}: the server draws no SVG - it writes the browser's scripts once a page
  // (core.js, then the files of the families drawn: pie.js and series.js for bar and line),
  // and per chart a span with the rows and the options as JSON, the rows as ordered pairs so
  // a key keeps its kind and place (2024 an integer key, as PHP has it), and a script that puts
  // the SVG in the span's place. The options are the browser's to read: the strict check
  // takes none of them for unread.

  $rows   = [ [ 'name' => 'Jan', 'n' => 5 ], [ 'name' => '2024', 'n' => '7.5', 2024 => 'x' ] ];
  $out    = padUnprotect ( padUnescape ( padCode ( "{chart:bar data='rows', label='name', value='n', title='Mine', client/}"
                                                 . "{chart:line data='rows', label='name', value='n', client/}" ) ) );

  preg_match_all ( '/data-pad-chart="([^"]*)"/', $out, $found );

  $specs  = array_map ( fn ( $s ) => json_decode ( htmlspecialchars_decode ( $s, ENT_QUOTES ), TRUE ), $found [1] );
  $answer = substr_count ( $out, '<script' ) . ' scripts, '
          . substr_count ( preg_replace ( '#<script\b[^>]*>.*?</script>#s', '', $out ), '<svg' ) . ' svg drawn, '
          . implode ( ' + ', array_map ( fn ( $s ) => $s ['kind'] . ' ' . ( preg_match ( '/^pad-chart-[0-9a-f]{8}$/', $s ['id'] ) ? 'id' : 'no id' )
                                                     . ' ' . json_encode ( $s ['rows'] ['m'] [1] [1] ['m'] ) . ' ' . implode ( ',', array_keys ( $s ['params'] ) ),
                                         $specs ) );

?>
