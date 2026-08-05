<?php

  // Implements toContent="name": moves the finished $padResult [$pad] into $padContentStore
  // under that name and blanks the result, so the level stores silently. The text is fetched
  // again with {content:name} or the content option. End-phase option.

  $padStoreName = $padPrm [$pad] ['toContent'];

  $padContentStore [$padStoreName] = $padResult [$pad];

  $padResult [$pad] = '';

?>