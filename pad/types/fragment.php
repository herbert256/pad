<?php

  $padOne = APP . "content/" . $padTag [$pad];

  if ( padIsContentFile ($padOne) )
    return include PAD . 'pad/build/one.php';

  return NULL;

?>