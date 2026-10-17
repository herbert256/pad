<?php

  // The scripts and styles of an {excerpt ..., html} text are left out in one pass: an
  // unclosed <script> before a megabyte of text emptied the excerpt (or ended the request),
  // and openers without a closer took seconds - every one scanned on to the end.

  include_once PAD . 'tags/_lib/excerpt.php';

  $big   = '<p>Hello</p><script>' . str_repeat ( 'x', 1000000 );
  $start = hrtime ( TRUE );
  $many  = strlen ( padExcerptCode ( str_repeat ( '<style>x ', 60000 ) ) );
  $time  = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $kept  = padExcerptCode ( 'a<script>x</script>b<STYLE>y</style >c<script src=1>d' );

?>
