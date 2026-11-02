<?php

  $root = dirname ( __DIR__, 2 );
  require "$root/www/_common/showcase-redirect.php";
  $routes = include "$root/www/showcase/_routes.php";
  $checks = 0;
  $assert = function ( $ok, $message ) use ( &$checks ) {
    $checks++;
    if ( ! $ok ) throw new RuntimeException ( $message );
  };

  foreach ( [
    [ 'toys', '/pad/toys/', 'life&seed=7', 'games', 'toys/', '/pad/games/?toys/life&seed=7' ],
    [ 'toys', '/pad/toys/life', 'seed=7', 'games', 'toys/', '/pad/games/?toys/life&seed=7' ],
    [ 'toys', '/pad/toys/index.php/life', '', 'games', 'toys/', '/pad/games/?toys/life' ],
    [ 'toys', '/pad/toys/life', 'boids&seed=2', 'games', 'toys/', '/pad/games/?toys/boids&seed=2' ],
    [ 'patterns', '/pad/patterns/topography&seed=8', '', 'images', '', '/pad/images/?topography&seed=8' ],
    [ 'sections', '/pad/sections/', 'effect/typewriter', 'widgets', '', '/pad/widgets/?effect/typewriter' ],
    [ 'sheets', '/pad/sheets/', '', 'tables', '', '/pad/tables/?index' ],
  ] as [ $old, $uri, $query, $app, $prefix, $expected ] ) {
    $got = padShowcaseDestination ( [ 'SCRIPT_NAME' => "/pad/$old/index.php", 'REQUEST_URI' => $uri, 'QUERY_STRING' => $query ], $app, $prefix );
    $assert ( $got === $expected, "Redirect: $uri?$query -> $got instead of $expected" );
  }
  $assert ( padShowcaseDestination ( [ 'SCRIPT_NAME' => '/toys/index.php', 'REQUEST_URI' => '/toys/', 'QUERY_STRING' => 'life' ], 'games', 'toys/' ) === '/games/?toys/life', 'Root mount' );
  $assert ( padShowcaseDestination ( [ 'SCRIPT_NAME' => '/pad/showcase/index.php', 'QUERY_STRING' => 'diagram/flow' ], routes: $routes ) === NULL, 'Unmoved example must stay in Showcase' );
  foreach ( $routes as $old => [ $app, $page ] ) {
    $assert ( is_file ( "$root/apps/$app/$page.pad" ), "Missing canonical example $app/$page" );
    $assert ( ! is_file ( "$root/apps/showcase/$old.pad" ), "Duplicate example $old" );
    $got = padShowcaseDestination ( [ 'SCRIPT_NAME' => '/pad/showcase/index.php', 'QUERY_STRING' => "$old&test=1" ], routes: $routes );
    $assert ( $got === "/pad/$app/?$page&test=1", "Moved example redirect $old" );
  }

  // Links embedded in a moved example must still resolve in its owner application.
  foreach ( $routes as $old => [ $app, $page ] ) {
    $source = file_get_contents ( "$root/apps/$app/$page.pad" );
    preg_match_all ( '/["\']\?([a-z]+\/[a-z_]+)/', $source, $links );
    foreach ( $links [1] as $link )
      $assert ( ! isset ( $routes [$link] ), "Old example link $link inside $app/$page" );
  }

  $cases = [
    [ 'games', 'index', [ '?toys/life', 'Games and toys' ] ],
    [ 'games', 'toys/life', [ 'pad-toy', 'Life', 'The template' ] ],
    [ 'images', 'index', [ '?filters', '?examples/avatar/team', 'Images, avatars and patterns' ] ],
    [ 'images', 'examples/avatar/team', [ 'demo-template', 'demo-result', 'pad-avatar' ] ],
    [ 'tables', 'index', [ '?functions', '?examples/datatable/orders', 'Tables and spreadsheets' ] ],
    [ 'tables', 'examples/datatable/orders', [ 'demo-data', 'demo-template', 'pad-datatable' ] ],
    [ 'widgets', 'index', [ '?sections', '?effects', '?themes', '?palettes', '?examples/tabs/product_page' ] ],
    [ 'widgets', 'examples/toc/article', [ 'demo-template', 'In this article', 'href="#grinding"' ] ],
    [ 'calendars', 'index', [ '?examples/countdown/launch', '?examples/timeago/comments' ] ],
    [ 'charts', 'index', [ '?examples/timeline/space' ] ],
  ];
  foreach ( $cases as [ $app, $page, $parts ] ) {
    $process = proc_open ( [ PHP_BINARY, "$root/editors/render.php", $app, $page ], [ [ 'pipe', 'r' ], [ 'pipe', 'w' ], [ 'pipe', 'w' ] ], $pipes, $root );
    fclose ( $pipes [0] );
    $html = stream_get_contents ( $pipes [1] ); fclose ( $pipes [1] );
    $error = stream_get_contents ( $pipes [2] ); fclose ( $pipes [2] );
    $assert ( proc_close ( $process ) === 0, "$app/$page: $error " . substr ( $html, 0, 200 ) );
    foreach ( $parts as $part ) $assert ( str_contains ( $html, $part ), "$app/$page: missing $part" );
    $assert ( substr_count ( strtolower ( $html ), '<!doctype html>' ) === 1, "$app/$page: nested application frames" );
    if ( $app === 'widgets' && $page === 'index' )
      $assert ( ! str_contains ( $html, 'href="?widgets/sections"' ), 'A subsystem directory is not a page route' );
  }
  echo "$checks showcase ownership, redirect and rendering checks passed\n";

?>
