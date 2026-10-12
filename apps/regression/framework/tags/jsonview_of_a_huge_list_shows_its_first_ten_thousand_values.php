<?php

  // A posted JSON list of 300 000 items: some 170 bytes of markup each, copied a few times -
  // 221 MB, past a memory_limit of 128 MB. The tree writes ten thousand values and says how
  // many more there are; the count of the list stays the whole.

  $huge  = padJsonviewHtml ( array_fill ( 0, 300000, 0 ), 0, '' );
  $items = substr_count ( $huge, '<li>' );
  $more  = preg_match ( '/… ([0-9]+) more/', $huge, $m ) ? $m [1] : 'none';
  $count = preg_match ( '/([0-9]+) items/', $huge, $m ) ? $m [1] : 'none';

?>
