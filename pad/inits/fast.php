<?php

  // Resolves a fast link - a short random key standing in for a whole set of request
  // variables that padFastLink() stored in the pad database's links table.
  //
  // When the query string names no existing page it is treated as such a key: the stored
  // variables are looked up, unserialised into globals, and the config defaults are re-read
  // because they may be among them.
  //
  // Currently disabled - the unconditional return on the first line short-circuits the whole
  // file, so fast links are not resolved at all.

  return;

  if ( padPageCheck ($padPage) )
    return;

  $padFast = padDb ( "field vars from links where link = '{1}'", [ 1 => $_SERVER['QUERY_STRING'] ] );

  if ( ! $padFast )
    padBootError ("Shortcut to stored parameters not found: |" . $_SERVER['QUERY_STRING'] . "|");

  extract ( unserialize ( $padFast ), EXTR_OVERWRITE );

  include PAD . 'config/config.php';

?>