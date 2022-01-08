<?php

  include PAD . "walk/go/walk.php";

  $pad_walk = 'end';

  $pad_content = $pad_result [$pad_lvl];

  $pad_result [$pad_lvl] = include PAD . "walk/go/go.php";

?>