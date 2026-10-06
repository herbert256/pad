<?php

  // The check the index runs when asked with &test, run every time. The crawl fetches this
  // page and compares it with its stored answer, so every suite run holds live reload to
  // its promise - the index alone only offers a Test link, which no run follows.

  $test = TRUE;

  include APP . 'index.php';

?>
