<?php

  // A text in Latin-1 - or a visitor's request value - holds a byte that is no UTF-8: it is
  // a character the notation has no sign for, as U+FFFD, and no TypeError of count().

  $word = "caf\xE9";

?>
