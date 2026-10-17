<?php

  // Type handler for a built-in tag ({if}, {while}, {pad:if}): loads PAD/tags/<name>.php and
  // .pad through types/_go/tag.php, exactly as an application tag is loaded.
  //
  // The functions only the tag calls stand in tags/_lib/<name>.php, included here the first
  // time the tag runs - pad/lib/ is loaded on every request, used or not. A tag that shares
  // code with another includes that one's file itself.

  if ( $padTagLib = padTagLib ( $padTag [$pad] ) )
    include_once $padTagLib;

  $padTagGo = PAD . 'tags/' . $padTag [$pad];

  return include PAD . 'types/_go/tag.php';

?>
