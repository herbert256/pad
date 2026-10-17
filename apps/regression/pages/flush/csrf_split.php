<?php

  // A {flush} inside a posting form: the form gets no CSRF token, so every post of it is
  // refused - the strict check says so, where the form went out without a word.

  $padCsrf = TRUE;

?>
