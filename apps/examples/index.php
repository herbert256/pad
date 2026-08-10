<?php

  // The search. $q arrives as a global from the form; the walk is over the .html files,
  // which every example has, and a query matches on the example's name first, then on the
  // contents of its three files.

  $q        = padMakeSafe ( $q ?? '', 200 );
  $searched = ( strlen ( $q ) >= 2 ) ? 1 : 0;
  $short    = ( $q != '' and ! $searched ) ? 1 : 0;
  $found    = [];
  $total    = 0;
  $capped   = 0;

  if ( $searched ) {

    $dir = DATA . 'examples/';

    // More than one word searches for each and keeps only the examples that hold them
    // all. Every word is placed in its best class - the example's name, inside a {...}
    // tag of the .pad, inside an html header, or plainly in the text - and the example
    // ranks by its weakest word: it is only as much about the query as its least-covered
    // word. All words in the name ranks above every content hit; within a content class
    // a small example ranks above a big one - a few lines that match are about the
    // query, a big page merely mentions it. The bucket is keyed class;size;name so one
    // ksort orders it all.

    $words = array_filter ( padExplode ( $q, ' ' ), 'strlen' );

    $foundName    = [];
    $foundContent = [];

    $directory = new RecursiveDirectoryIterator ( $dir );
    $iterator  = new RecursiveIteratorIterator  ( $directory );

    foreach ( $iterator as $one ) {

      $path = padCorrectPath ( $one->getPathname () );

      if ( ! str_ends_with ( $path, '.html' ) )
        continue;

      $item = substr ( $path, strlen ( $dir ), -5 );

      $size  = 0;
      $texts = [];

      foreach ( [ 'html', 'pad', 'php' ] as $ext ) {
        $texts [$ext] = padFileGet ( $dir . "$item.$ext" );
        $size        += strlen ( $texts [$ext] );
      }

      preg_match_all ( '/\{[^\s{}][^{}]*\}/', $texts ['pad'], $padTags );
      preg_match_all ( '/<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', $texts ['pad'] . $texts ['html'], $padHeads );

      $inTags  = implode ( ' ', $padTags  [0] );
      $inHeads = implode ( ' ', $padHeads [1] );
      $inAll   = implode ( ' ', $texts );

      $worst = 0;

      foreach ( $words as $word ) {

        if     ( stripos ( $item,    $word ) !== FALSE ) $class = 0;
        elseif ( stripos ( $inTags,  $word ) !== FALSE ) $class = 1;
        elseif ( stripos ( $inHeads, $word ) !== FALSE ) $class = 2;
        elseif ( stripos ( $inAll,   $word ) !== FALSE ) $class = 3;
        else   { $worst = 9; break; }

        $worst = max ( $worst, $class );

      }

      if ( $worst == 9 )
        continue;

      if ( $worst == 0 )
        $foundName [$item] = [ 'item' => $item ];
      else
        $foundContent [ sprintf ( '%d;%012d;%s', $worst, $size, $item ) ] = [ 'item' => $item ];

    }

    ksort ( $foundName );
    ksort ( $foundContent );

    $found = $foundName + $foundContent;

    $total = count ( $found );

    if ( $total > 200 ) {
      $found  = array_slice ( $found, 0, 200 );
      $capped = 200;
    }

  }

  $results = array_values ( $found );

  $title = 'Examples';

?>