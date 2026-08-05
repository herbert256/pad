<?php

  // Works out which page this request runs, and fails the request early if there is none.
  //
  // The name comes from whatever source applies: an already-set $padPage (a restart, or an
  // entry point that hard-codes it), otherwise the first query string key - PAD's URLs are
  // ?page/subpage, not path based - otherwise the first command line argument, otherwise
  // 'index'.
  //
  // The name is then verified against the application directory and resolved, and $padDir
  // (the page's subdirectory) and $padPath are derived from it; build/ walks those to collect
  // the _lib, _inits.pad and _exits.pad of every level. $padStartPage remembers the page the
  // request began with, so a restart can still tell where it came from.

  if     ( isset($padPage) )                $padPage = $padPage;
  elseif ( count($_GET) )                   $padPage = array_key_first ($_GET);
  elseif ( isset ( $_SERVER['argv'] [1] ) ) $padPage = $_SERVER['argv'] [1];
  else                                      $padPage = 'index';

  $padPage = padCorrectPath ( $padPage );

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPage ($padPage);
  $padDir  = padDir  ();
  $padPath = padPath ();

  if ( ! isset ( $padStartPage) )
    $padStartPage = $padPage;

?>