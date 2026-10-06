<?php

  // Quotes and list items nest by calling the block reader again, each level holding a
  // copy of its lines: 60000 > in a row asked for gigabytes. Past twenty levels the rest
  // is the text of a paragraph.

  $before = memory_get_usage ();
  $quotes = padMarkdown ( str_repeat ( '>', 60000 ) . ' deep' );
  $lists  = padMarkdown ( str_repeat ( '- ', 30000 ) . 'deep' );
  $used   = ( memory_get_peak_usage () - $before < 64000000 ) ? 'in bounds' : 'too much memory';

  $result = $used . ' ' . substr_count ( $quotes, '<blockquote>' ) . ' ' . substr_count ( $lists, '<ul>' )
          . ' ' . substr ( trim ( strip_tags ( $quotes ) ), -12 ) . ' ' . substr ( trim ( strip_tags ( $lists ) ), -8 );

?>
