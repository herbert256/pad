<?php

  // Implements toContent="name": moves the finished text into $padContentStore under that
  // name and blanks it, so the level stores silently. The text is fetched again with
  // {content:name} or the content option. End-phase option.
  //
  // An end-phase handler works on $padContent - the walker copies the result in before the
  // phase and back out after it, so blanking $padResult directly, as this used to, was
  // silently undone and the level printed what it was documented to store silently.

  $padStoreName = $padPrm [$pad] ['toContent'];

  $padContentStore [$padStoreName] = $padContent;

  $padContent = '';

?>