<?php

  // {highlight 'php'}...{/highlight} - the content as source code, coloured on the server
  // by lib/highlight.php: pad, php, html (xml, svg), css, js (ts), json, yaml, sql or bash.
  // The content is taken as it stands, before the level walks it - the braces of PHP, CSS,
  // JSON or of PAD itself need no {ignore} - and the indent its lines share is taken off.
  // A {# comment #} is gone before any tag sees it, so a PAD sample keeps none.
  //
  // file= colours a file of the application instead - 'orders.php', '_data/products.json'
  // - never one outside it, under _config/ or a dotfile; the language then defaults to the
  // file's extension. lines numbers the lines, mark='3, 5-7' sets lines apart.

  $padHighlightFile = trim ( (string) padTagParm ( 'file' ) );
  $padHighlightLang = trim ( (string) $padParm );

  if ( $padHighlightFile !== '' ) {

    $padHighlightApp  = realpath ( APP );
    $padHighlightPath = realpath ( APP . ltrim ( $padHighlightFile, '/' ) );

    if ( $padHighlightPath === FALSE or ! is_file ( $padHighlightPath ) or ! str_starts_with ( $padHighlightPath, $padHighlightApp . DIRECTORY_SEPARATOR )
         or preg_match ( '#(^|/)(_config/|\.)#', substr ( $padHighlightPath, strlen ( $padHighlightApp ) + 1 ) ) ) {
      if ( $padCheckSyntax )
        padError ( "there is no file '" . padMakeSafe ( $padHighlightFile, 60 ) . "' of the application to highlight" );
      return '';
    }

    $padHighlightText = file_get_contents ( $padHighlightPath );

    if ( $padHighlightLang === '' )
      $padHighlightLang = pathinfo ( $padHighlightPath, PATHINFO_EXTENSION );

  } else
    $padHighlightText = $padContent;

  $padContent = '';

  if ( $padHighlightLang === '' )
    $padHighlightLang = 'text';

  if ( ! padHighlightKnown ( $padHighlightLang ) and $padCheckSyntax )
    padError ( "there is no language '" . padMakeSafe ( $padHighlightLang, 20 ) . "' to highlight - pad, php, html, css, js, json, yaml, sql or bash" );

  return padHighlight ( $padHighlightText, $padHighlightLang, (bool) padTagParm ( 'lines', FALSE ), (string) padTagParm ( 'mark' ) );

?>
