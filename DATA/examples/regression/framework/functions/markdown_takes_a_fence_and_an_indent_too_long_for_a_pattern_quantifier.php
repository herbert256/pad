<?php

  // A fence of 70000 backticks and a line indented 65536 spaces: the closing fence and the
  // dedent were patterns with that number as a quantifier, which PCRE refuses past 65535 -
  // a warning, so the request answered 500.

  $fence  = padMarkdown ( str_repeat ( '`', 70000 ) . "\nx" );
  $indent = padMarkdown ( str_repeat ( ' ', 65536 ) . 'y' );

  $result = trim ( str_replace ( "\n", ' ', strip_tags ( $fence ) ) ) . '|' . strip_tags ( $indent );

?>
