<?php

  pad_trace ("one/start", "one=$pad_one" );

  $pad_one_return = pad_get_html ( "$pad_one.html" , TRUE);

  pad_trace ("one/end", $pad_one_return);

  return $pad_one_return;

?>