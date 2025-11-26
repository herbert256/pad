<?php

  foreach ( padDirs () as $padK => $padV ) {

    $padContentPad = APP2 . $padV . $padcontentLoop . "$padContentName.pad";
    $padContentPhp = APP2 . $padV . $padcontentLoop . "$padContentName.php";

    if ( file_exists ( $padContentPad ) or file_exists ( $padContentPhp ) ) {

      $padContentData = '';

      if ( file_exists ( $padContentPhp ) ) {
        $padCall = $padContentPhp;
        $padContentData .= include 'call/obNoOne.php';
      }

      if ( file_exists ( $padContentPad ) ) 
        $padContentData .= padFileGet ( $padContentPad );

      return $padContentData;
      
    }

  }

?>