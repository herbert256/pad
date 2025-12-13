<?php
 
  if ( padStoreCheck      ( $padGetName ) ) return include PAD . 'get/content.php';
  if ( padAppIncludeCheck ( $padGetName ) ) return include PAD . 'get/include.php';
  if ( padAppPageCheck    ( $padGetName ) ) return include PAD . 'get/page.php';

  $padContentSearch = padTypeTag ( $padGetName );

  return '';

?>
