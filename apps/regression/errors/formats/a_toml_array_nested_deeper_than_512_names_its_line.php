<?php

  // Arrays and inline tables nest at most 512 deep, as JSON does: 200000 brackets are the
  // text's error, where the reader recursed until PHP's stack ran out.

  $ini = padFormatRead ( 'toml', 'a = ' . str_repeat ( '[', 200000 ) . str_repeat ( ']', 200000 ) );

?>
