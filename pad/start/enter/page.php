<?php

  $padStrPag [$pad] [0] = $padPage;
  $padStrPag [$pad] [1] = $padInclude;
  $padStrPag [$pad] [2] = $padDir;
  $padStrPag [$pad] [3] = $padPath;

  $padPage    = $padParm;
  $padInclude = padTagParm ( 'include', TRUE );
  $padDir     = padDir ();  
  $padPath    = padPath ();  

  $padStrBld = 'page';
  $padStrCod = '';
  $padStrRet = include pad . "start/parms.php";

  $padPage    = $padStrPag [$pad] [0];
  $padInclude = $padStrPag [$pad] [1];
  $padDir     = $padStrPag [$pad] [2];
  $padPath    = $padStrPag [$pad] [3];
  
  return $padStrRet;

?>