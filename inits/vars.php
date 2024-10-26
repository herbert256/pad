<?php

  $pad          = -1;
  $padLvlId     = 0;
  $padRestart   = '';     
  $padOutput    = '';
  $padStop      = '000';
  $padEtag      = '';
  $padLen       = 0;
  $padTime      = $_SERVER ['REQUEST_TIME'];  
  $padCacheStop = 0;
  $padPageLevel = [];
  $padBuffer    = '';
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;
  $padStrCnt    = -1;
  $padStrFunCnt = 0;
  $padInfo      = '';
  $padInfoCnt   = 0;
  $padEvalCnt   = -1;
 
?>