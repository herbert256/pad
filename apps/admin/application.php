<?php

  // One application: what it is, what it has, its pages, and what is going on with it under
  // DATA/ - with the actions that go with it. Taking it down and bringing it up are posts
  // here; the pad commands for it are a click away on the console page.

  $app = adminAppAsked ();

  if ( $app === '' )
    padRedirect ( 'apps' );

  if ( adminPost () ) {

    $action = adminField ( 'action' );

    if ( $action == 'down' and $app !== 'admin' and $app !== '_common' ) {
      padMaintenanceDown ( $app, adminField ( 'secret' ), (int) adminField ( 'retry', '60' ), adminField ( 'message' ) );
      adminDone ( "$app is down for maintenance.", 'application', [ 'app' => $app ] );
    }

    if ( $action == 'up' ) {
      padMaintenanceUp ( $app );
      adminDone ( "$app is up again.", 'application', [ 'app' => $app ] );
    }

  }

  $info  = adminAppInfo ( $app );
  $title = $app;

  $isApp     = $app !== '_common' ? 1 : 0;
  $canDown   = ( $isApp and $app !== 'admin' ) ? 1 : 0;
  $downInfo  = padMaintenanceRead ( $app );
  $isDown    = $downInfo ? 1 : 0;
  $downSince = $downInfo ? adminAgo ( $downInfo ['time'] ) : '';
  $downText  = $downInfo ['message'] ?? '';

  $appLink     = $info ['link'];
  $description = $info ['description'];
  $parts       = $info ['parts'];
  $kinds       = $info ['kinds'];

  $facts = [
    [ 'label' => 'Directory',      'value' => 'apps/' . $app . '/' ],
    [ 'label' => 'Web directory',  'value' => $info ['www'] === '' ? 'none' : "www/$app/" ],
    [ 'label' => 'Files',          'value' => $info ['files'] . ' files, ' . $info ['size'] ],
    [ 'label' => 'Lines',          'value' => number_format ( $info ['lines'] ) . ' in .pad, .php, .html, .js and .css' ],
    [ 'label' => 'Changed last',   'value' => $info ['newestFile'] === '' ? '' : $info ['newestFile'] . ', ' . adminAgo ( $info ['newest'] ) ]
  ];

  // What DATA/ holds for it, each with the page that shows more.

  $state = [
    [ 'label' => 'Jobs queued',  'value' => $info ['queued'],     'page' => "queues&app=$app" ],
    [ 'label' => 'Jobs failed',  'value' => $info ['failed'],     'page' => "queues&app=$app" ],
    [ 'label' => 'Log files',    'value' => $info ['logs'],       'page' => "logs&app=$app" ],
    [ 'label' => 'Error dumps',  'value' => $info ['dumps'],      'page' => "dumps&app=$app" ],
    [ 'label' => 'Mails',        'value' => $info ['mails'],      'page' => "mail&app=$app" ],
    [ 'label' => 'Migrations',   'value' => $info ['migrations'], 'page' => "console&app=$app&command=migrate-status" ],
    [ 'label' => 'Tests',        'value' => $info ['tests'],      'page' => "console&app=$app&command=test" ]
  ];

  // The pages, each with a link that opens it: a bracketed route has no address of its own.

  $pageRows = [];

  if ( $isApp )
    foreach ( adminAppPages ( $app ) as $one )
      $pageRows [] = $one + [
        'href'  => $one ['route'] ? '' : $appLink . ( $one ['page'] === 'index' ? '' : '?' . $one ['page'] ),
        'file'  => $one ['pad'] ? $one ['page'] . '.pad' : ( $one ['html'] ? $one ['page'] . '.html' : $one ['page'] . '.php' )
      ];

  $pageCount = count ( $pageRows );

  // The README, as Markdown - its own raw HTML escaped.

  $readmeFile = APPS . "$app/README.md";
  $readme     = is_file ( $readmeFile ) ? padMarkdown ( (string) file_get_contents ( $readmeFile ) ) : '';
  $hasReadme  = $readme !== '' ? 1 : 0;

  // The settings its _config/config.php makes.

  $configRows  = adminConfigOf ( $app );
  $configCount = count ( $configRows );
  $partCount   = count ( $parts );

?>
