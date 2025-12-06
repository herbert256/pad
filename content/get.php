<?php

  if ( padStoreCheck   ( $padContentName ) ) return include PAD . 'content/store.php';
  if ( padContentCheck ( $padContentName ) ) return include PAD . 'content/content.php';
  if ( padPadFileCheck ( $padContentName ) ) return include PAD . 'content/page.php';

  return '';

?>