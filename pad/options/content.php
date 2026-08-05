<?php

  // Implements content="name": resolves the name through get/content.php - content store, app
  // _include file, app page, or the tag rendered as a function - and returns the text.
  //
  // Included by level/go.php, which merges the returned text into the level's content.

  $padGetName = padTagParm ( 'content' );

  return include PAD . 'get/content.php';

?>