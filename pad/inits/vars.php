<?php

  $pad          = -1;
  $padLvlId     = 0;
  $padApp       = 0;
  $padRestart   = '';
  $padOutput    = '';
  $padStop      = '000';
  $padEtag      = '';
  $padLen       = 0;
  $padTime      = $_SERVER ['REQUEST_TIME'];
  $padCacheStop = 0;
  $padPageLevel = [];
  $padInclude   = isset ( $_REQUEST ['padInclude'] ) ? TRUE : FALSE;
  $padStrCnt    = -1;
  $padStrFunCnt = 0;
  $padInfo      = '';
  $padInfoCnt   = 0;
  $padEvalCnt   = -1;

  $padProviders = [];

  if ( ! isset ( $pqStore )     ) $pqStore     = [];
  if ( ! isset ( $padLastPush ) ) $padLastPush = '';
  if ( ! isset ( $padLastPull ) ) $padLastPull = '';

  $padPost = ( isset ( $_SERVER['REQUEST_METHOD'] ) and $_SERVER['REQUEST_METHOD'] == 'POST' );

?>