<?php

  // Type handler for a built-in tag ({if}, {while}, {pad:if}): loads PAD/tags/<name>.php and
  // .pad through types/_go/tag.php, exactly as an application tag is loaded.

  $padTagGo = PAD . 'tags/' . $padTag [$pad];

  return include PAD . 'types/_go/tag.php';

?>