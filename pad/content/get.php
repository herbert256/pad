<?php

  if ( padStoreCheck   ( $padContentName ) ) return include 'content/store.php';
  if ( padContentCheck ( $padContentName ) ) return include 'content/content.php';
  if ( padPadFileCheck ( $padContentName ) ) return include 'content/page.php';

  return '';

?>