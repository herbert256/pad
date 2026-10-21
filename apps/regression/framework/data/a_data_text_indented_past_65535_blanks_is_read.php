<?php

  // Every line indented by 70000 blanks: the common indent is more than a counted
  // quantifier of PCRE may hold.

  $v = str_repeat ( ' ', 70000 ) . "a\n" . str_repeat ( ' ', 70000 ) . 'b';

?>
