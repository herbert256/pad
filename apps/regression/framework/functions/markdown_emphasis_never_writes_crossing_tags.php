<?php

  // Strong and em runs that overlap: an em pass opened inside a <strong> and closed after
  // it. Emphasis inside emphasis is kept.

  $nested   = '**foo **bar** baz**';
  $crossing = '**bold *and** italic*';
  $inside   = '*a **b** c* and **a *b* c**';

?>
