<?php

  // Callback mode: before

  pad_callback_before_xxx ('init');

  foreach ( $pad_data [$pad] as $pad_k => $pad_v)
    pad_callback_before_row ( $pad_data [$pad] [$pad_k] );

  pad_callback_before_xxx ('exit'); 

?>