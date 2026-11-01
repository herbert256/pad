<?php

  // An inline table in an inline table, 100000 deep, is the text's error past 512, where
  // the reader recursed until PHP's stack ran out.

  $ini = padFormatRead ( 'toml', 'a = ' . str_repeat ( '{b = ', 100000 ) . '1' . str_repeat ( '}', 100000 ) );

?>
