<?php

  // The error reports the 'dump' error action and the try guards leave under DATA/dumps/ -
  // a directory per report, below the application and the page it happened on, its
  // _ERROR.html saying what went wrong and the other files the state of the request. A
  // report's files are shown in a sandboxed frame: they are HTML the engine wrote, and
  // nothing in them runs here.

  $title  = 'Error dumps';
  $root   = DATA . 'dumps/';
  $report = adminRelative ( adminGet ( 'report', adminField ( 'report' ) ) );
  $part   = adminRelative ( adminGet ( 'part' ) );

  if ( $report !== '' and ! is_file ( $root . "$report/_ERROR.html" ) )
    $report = '';

  if ( adminPost () ) {

    if ( adminField ( 'action' ) == 'delete' and $report !== '' ) {
      adminRemove ( $root . $report );
      adminDone ( 'The report is removed.', 'dumps' );
    }

    if ( adminField ( 'action' ) == 'delete-all' ) {
      $count = 0;
      foreach ( glob ( $root . '*', GLOB_ONLYDIR ) ?: [] as $dir )
        $count += adminRemove ( $dir );
      adminDone ( "Every report is removed - $count files.", 'dumps' );
    }

  }

  // Every report: a directory with an _ERROR.html, the application it belongs to read from
  // the start of its path.

  $reportRows = [];

  if ( is_dir ( $root ) ) {

    $walk = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $root, FilesystemIterator::SKIP_DOTS ) );

    foreach ( $walk as $one ) {

      if ( $one->getFilename () !== '_ERROR.html' )
        continue;

      $rel = substr ( str_replace ( '\\', '/', $one->getPath () ), strlen ( $root ) );

      $owner = '';

      foreach ( array_keys ( adminApps () ) as $name )
        if ( str_starts_with ( "$rel/", "$name/" ) and strlen ( $name ) > strlen ( $owner ) )
          $owner = $name;

      $inside = $owner === '' ? $rel : substr ( $rel, strlen ( $owner ) + 1 );

      $reportRows [] = [ 'report' => $rel, 'app' => $owner, 'page' => dirname ( $inside ) === '.' ? '' : dirname ( $inside ),
                         'time' => $one->getMTime (), 'when' => adminWhen ( $one->getMTime () ),
                         'error' => mb_strimwidth ( trim ( html_entity_decode ( strip_tags ( (string) file_get_contents ( $one->getPathname () ) ) ) ), 0, 240, '...' ),
                         'current' => $rel === $report ? 1 : 0 ];

    }

  }

  usort ( $reportRows, fn ( $a, $b ) => $b ['time'] <=> $a ['time'] );

  $reportCount = count ( $reportRows );

  // One report: its files as tabs, the one asked for (_ERROR.html first) in the frame.

  $partRows = [];
  $partHtml = '';

  if ( $report !== '' ) {

    $title = 'Error dump ' . basename ( $report );

    foreach ( scandir ( $root . $report ) ?: [] as $file )
      if ( $file [0] !== '.' and is_file ( $root . "$report/$file" ) )
        $partRows [] = [ 'part' => $file, 'label' => preg_replace ( '/\.(html|txt|json)$/', '', $file ) ];

    usort ( $partRows, fn ( $a, $b ) => [ $a ['part'] !== '_ERROR.html', $a ['part'] ] <=> [ $b ['part'] !== '_ERROR.html', $b ['part'] ] );

    if ( $part === '' or ! is_file ( $root . "$report/$part" ) )
      $part = '_ERROR.html';

    foreach ( $partRows as &$one )
      $one ['current'] = $one ['part'] === $part ? 1 : 0;

    unset ( $one );

    $partText = (string) file_get_contents ( $root . "$report/$part" );
    $partHtml = str_ends_with ( $part, '.html' ) ? $partText : '<pre>' . htmlspecialchars ( $partText ) . '</pre>';

  }

  $viewing = $report !== '' ? 1 : 0;

?>
