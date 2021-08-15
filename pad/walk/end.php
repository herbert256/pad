<?php

  include PAD_HOME . "pad/walk/go/walk.php";

  $pad_walk = 'end';

  $pad_content = $pad_result [$pad_lvl];

  $pad_result [$pad_lvl] = include PAD_HOME . "pad/walk/go/go.php";

?>