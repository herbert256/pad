<?php

  if ( isset ( $padContentStore [$padGetName] ) )
    return $padContentStore [$padGetName];

  if ( padAppIncludeCheck ( $padGetName ) ) return include PAD . 'get/include.php';
  if ( padAppPageCheck    ( $padGetName ) ) return include PAD . 'get/page.php';

  if ( padTypeTag ( $padGetName ) )
    return padTagAsFunction ( $padGetName, $padContent, [] );

  return '';

?>
