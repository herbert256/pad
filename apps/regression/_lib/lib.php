<?php

  function getPage ( $page, $ignoreErrors=0, $include=1 ) {

    global $padHost, $padScript;

    if ($include) $include = '&padInclude';
    else          $include = '';

    $url  = "$padHost$padScript?$page$include";
    $curl = padCurl ($url);

    if ( ! $ignoreErrors and ! str_starts_with ( $curl ['result'], '2') )
      return padError ("Curl failed: $url");

    return $curl;

  }
  
?>