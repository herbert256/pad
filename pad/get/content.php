<?php

  // Resolves the name in $padGetName to content and returns it: a stored {content} block
  // first, then an app _include/ snippet, then an app page, then a tag rendered as a
  // function; '' when nothing of that name exists.
  //
  // Reached from types/content.php, the content= option and options/go/reset.php; the
  // return value of the include is the content.

  if ( isset ( $padContentStore [$padGetName] ) )
    return $padContentStore [$padGetName];

  if ( padAppIncludeCheck ( $padGetName ) ) return include PAD . 'get/include.php';
  if ( padAppPageCheck    ( $padGetName ) ) return include PAD . 'get/page.php';

  if ( padTypeTag ( $padGetName ) )
    return padTagAsFunction ( $padGetName, $padContent, [] );

  return '';

?>