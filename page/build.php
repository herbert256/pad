<?php

  global $pad, $padBase;

  $padPage = $page;

  $GLOBALS['padIgnoreInclude'] = TRUE;
  include pad . 'build/build.php'; 
  unset ( $GLOBALS['padIgnoreInclude'] );

  return $padBase [$pad];

?>