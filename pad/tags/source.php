<?php

  // {source} - the files behind a page as tabs, coloured on the server (lib/sources.php):
  // the page asked for, or the one named first, with its template and PHP, then the files
  // files= adds - 'a/b.php, www:app.js', www: one in the application's www/ directory - or
  // only= those files alone, a text with commas either way.

  $padSourceOnly  = padSourceList ( padTagParm ( 'only',  '' ) );
  $padSourceFiles = padSourceList ( padTagParm ( 'files', '' ) );
  $padSourcePage  = trim ( (string) $padParm ) !== '' ? (string) $padParm : $padPage;

  return padSourceTabs ( $padSourcePage, $padSourceFiles, $padSourceOnly );

?>
