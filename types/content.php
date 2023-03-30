<?php

  $padContentGo = $padTag [$pad];

  if ( isset ( $padContentStore [$padContentGo] ) )
    return $padContentStore [$padContentGo];
  
  return include pad . 'options/go/content.php';

?>