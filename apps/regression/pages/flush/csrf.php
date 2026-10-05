<?php

  // With $padCsrf on, a flushed page carries the session's token in its forms on both sides
  // of the {flush}: the part sent early goes through the same form check as the rest, and
  // the session starts with the first flush, while its cookie can still go out - a form
  // below the flush asked for it after the headers had gone, and got an empty token.

  $padCsrf = TRUE;

?>
