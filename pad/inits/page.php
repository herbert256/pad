<?php

  if     ( isset($padPage) )                $padPage = $padPage;
  elseif ( count($_GET) )                   $padPage = array_key_first ($_GET);
  elseif ( isset ( $_SERVER['argv'] [1] ) ) $padPage = $_SERVER['argv'] [1];
  else                                      $padPage = 'index';

  $padPage = padCorrectPath ( $padPage );

  if ( str_ends_with($padPage, '.pad') ) $padPage = str_replace('.pad', '', $padPage);
  if ( str_ends_with($padPage, '.php') ) $padPage = str_replace('.php', '', $padPage);

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  $padDir  = padDir ();
  $padPath = padPath ();

  if ( ! isset ( $padStartPage) )
    $padStartPage = $padPage;

?>