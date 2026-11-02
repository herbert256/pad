<?php

  // The live application makes what it is given a page of this server and runs it - a
  // request like any other, its PHP and its template - so it answers this machine only
  // (_guard.php), and a post carries the CSRF token of the form it came from: the editor's
  // when it saves a page, the page's own when the page posts back. Apart from that its pages
  // are pages of an application like most others: PHP functions, request values, sessions,
  // and _common - its tags ({common:example}, {common:demo}), its library and its demo database - so the
  // examples of the manual and the other applications with _common run here as they run
  // there, the ones that read the database too.

  $padCsrf    = TRUE;
  $padTidy    = FALSE;

  // The former Playground renderer is now ?render in this app. Its nested template
  // keeps the same limits, independently of the full pages served under ?page/<id>.
  if ( $padPage === 'render' ) {
    $padCommon       = FALSE;
    $padPhpFunctions = [];
    $padRequestVars  = [];
    $padSqlHost      = 'localhost';
    $padSqlDatabase  = 'live_preview_none';
    $padSqlUser      = 'live_preview_none';
    $padSqlPassword  = '';
  }

  // The editor component of the boxes, as in WASM: Monaco's AMD build, from its
  // CDN or from a copy of its min/vs directory under www/; empty, the boxes stay plain text
  // fields. Monaco runs in the editor's own page, so the address names one version of it.

  $liveMonaco = 'https://cdn.jsdelivr.net/npm/monaco-editor@0.57.0/min/vs';

?>
