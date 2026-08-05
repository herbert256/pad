<?php

  function getPage ( $page, $ignoreErrors=0, $include=1 ) {

    global $padGoExt;

    if ($include) $include = '&padInclude';
    else          $include = '';

    $url  = "$padGoExt$page$include";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;

  }
  
?>