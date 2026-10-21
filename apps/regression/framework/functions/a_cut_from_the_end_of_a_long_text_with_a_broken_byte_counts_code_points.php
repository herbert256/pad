<?php

  // A text with a byte that is no UTF-8, far before its end: it is cut in code points, as
  // the same text is when the byte stands near the end.

  $tail  = "ab\u{1F1F3}\u{1F1F1}e\u{301}";
  $long  = "\xFF" . str_repeat ( 'a', 5000 ) . $tail;
  $short = "\xFF" . str_repeat ( 'a', 10 ) . $tail;

?>
