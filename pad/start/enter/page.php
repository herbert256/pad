<?php

  $padStrSav [$pad] [0] = $padPage;
  $padStrSav [$pad] [1] = $padInclude;
  $padStrSav [$pad] [2] = $padDir;
  $padStrSav [$pad] [3] = $padPath;

  $padPage    = $padParm;
  $padInclude = padTagParm ( 'include', TRUE );
  $padDir     = padDir ();  
  $padPath    = padPath ();  

  $padStrBld       = 'page';
  $padStrCod       = '';
  $padStrReturn = include pad . "start/parms.php";

  $padPage    = $padStrSav [$pad] [0];
  $padInclude = $padStrSav [$pad] [1];
  $padDir     = $padStrSav [$pad] [2];
  $padPath    = $padStrSav [$pad] [3];

  return $padStrReturn;

?>