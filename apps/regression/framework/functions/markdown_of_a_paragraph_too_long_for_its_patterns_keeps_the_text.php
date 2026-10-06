<?php

  // Over a megabyte in one paragraph: a code span or link pattern runs into PCRE's
  // backtrack limit and answers NULL, which the next pass was handed as its subject - a
  // deprecation, so a 500 for a long comment, and the paragraph gone.

  $code = padMarkdown ( '`' . str_repeat ( 'a', 1100000 ) );
  $link = padMarkdown ( '[' . str_repeat ( 'b', 1100000 ) . '](x)' );

  $result = strlen ( $code ) . ' ' . substr ( $code, 3, 3 ) . ' ' . strlen ( $link ) . ' ' . substr ( $link, 3, 3 );

?>
