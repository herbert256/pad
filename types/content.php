<?php

  $padContentGo = $padTag [$pad];

  if ( isset ( $padContentStore [$padContentGo] ) )
    return $padContentStore [$padContentGo];
  
  return include PAD . 'options/go/content.php';

?>