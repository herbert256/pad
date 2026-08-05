<?php

  // Runs the end phase of the option walk - the padOptionsEnd list: toBool, toContent, toData,
  // tidy, dump - over the finished $padResult [$pad]. Included by level/end.php as the level
  // closes, just before the closing pipes.

  $padOptions = 'end';

  include PAD . 'options/go/options.php';

?>