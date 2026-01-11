<?php

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