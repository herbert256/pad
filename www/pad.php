<?php

  include __DIR__ . '/../home/home.php';

  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  $padDir  = dirname  ( $_SERVER ['SCRIPT_NAME'] ) ;
  $padApp  = basename ( $padDir );
  $padRoot = dirname  ( $padDir );

  include "$padHome/pad/pad.php";

?>