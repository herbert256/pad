<?php

  include pad . 'page/setup.php';
  include pad . 'build/build.php';   
  include pad . 'page/level.php'; 

  $padContentSave = $padContent;
  $padContent = $padHtml [$pad+1];

  include pad . 'exits/output.php';

  $padContentReturn = $padContent;
  $padContent = $padContentSave;
 
  return $padContentReturn;

?>