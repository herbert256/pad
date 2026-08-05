<?php

  // Type handler for a tag borrowed from the shared _common app: loads COMMON/_tags/<name> as a
  // tag pair through types/_go/tag.php, or, when the name is a _common/_include/ snippet
  // instead, reads that snippet through get/go/call.php.
  //
  // Returns TRUE when neither exists, leaving the level a plain hit with no value of its own.

  if ( padCommonTagCheck ( $padTag [$pad] ) ) {

    $padTagGo = COMMON . '_tags/' . $padTag [$pad];

    return include PAD . 'types/_go/tag.php';

  }

  if ( padCommonIncludeCheck  ( $padTag [$pad] ) ) {

    $padGetCall = COMMON . '_include/' . $padTag [$pad];

    return include PAD . 'get/go/call.php';

  }

  return TRUE;

?>