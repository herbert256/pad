<?php

  // Only the names of $padSessionVars come out of the session as variables - member here.
  // Every key of it did, once the application declared any: a value another application
  // on the host kept in the shared session, foreignAdmin = yes, preset $foreignAdmin in
  // this one, ahead of anything the request or the page said.

  $foreignBase = $padHost . 'regression/clean_urls/index.php/';
  $foreignPut  = padCurl ( $foreignBase . 'session_foreign_put?padInclude' );
  $foreignJar  = [ 'PHPSESSID' => $foreignPut ['cookies'] ['PHPSESSID'] ?? '' ];
  $foreignGet  = padCurl ( [ 'url' => $foreignBase . 'session_foreign_get?padInclude', 'cookies' => $foreignJar ] );

  $foreignResult = trim ( $foreignPut ['data'] ) . ' | ' . trim ( $foreignGet ['data'] );

?>
