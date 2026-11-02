<?php

  // {pad:markdown $text} with an empty value - a comment nobody wrote - renders nothing, where
  // it failed as "the pair {pad:markdown} never closes": the single form asked whether its
  // value was empty, not whether a parameter was written.

  $empty = '';
  $space = ' ';

?>
