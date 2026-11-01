<?php

  // With the strict check off a JSON text sequence recovers as RFC 7464 (2.3, 2.4) has a
  // parser recover - and as jq --seq reads the same text: the text before the first RS, a
  // record that is no JSON and a number with no white space before the next RS are passed
  // over, every other record read; true behind its line feed, and the last record, read.

  $padCheckSyntax = FALSE;

  $lenientSeq = json_encode ( padFormatRead ( 'jsonseq', "junk\x1E{\n  \"a\": 1\n}\n\x1E\x1E[1,2]\n\x1E12\x1E\"str\"\n\x1E{\"b\":\n\x1Etrue\n\x1E3" ) );

?>
