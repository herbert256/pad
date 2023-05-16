<?php

  $padContentGo = padTagParm('content');

  if ( isset ( $padContentStore [$padContentGo] ) )
    return $padContentStore [$padContentGo];
  
  return include pad . 'options/content.php';

?>