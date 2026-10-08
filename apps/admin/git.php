<?php

  // The checkout the installation runs from, read with git: the branch and how it stands
  // to its upstream, what is changed and not committed, and the last commits. Reading only.

  $title = 'Git';

  [ $code ]  = adminGit ( [ 'rev-parse', '--is-inside-work-tree' ] );
  $isGit     = $code === 0 ? 1 : 0;
  $facts     = $changeRows = $commitRows = [];

  if ( $isGit ) {

    $branch   = trim ( adminGit ( [ 'rev-parse', '--abbrev-ref', 'HEAD' ] ) [1] );
    $upstream = adminGit ( [ 'rev-parse', '--abbrev-ref', '--symbolic-full-name', '@{u}' ] );
    $counts   = $upstream [0] === 0 ? trim ( adminGit ( [ 'rev-list', '--left-right', '--count', 'HEAD...@{u}' ] ) [1] ) : '';
    $remote   = trim ( adminGit ( [ 'remote', 'get-url', 'origin' ] ) [1] );

    [ $ahead, $behind ] = array_pad ( preg_split ( '/\s+/', $counts ), 2, '0' );

    $facts = [
      [ 'label' => 'Branch',   'value' => $branch ],
      [ 'label' => 'Upstream', 'value' => $upstream [0] === 0 ? trim ( $upstream [1] ) . " - $ahead ahead, $behind behind" : 'none' ],
      [ 'label' => 'Remote',   'value' => preg_replace ( '#//[^/@]+@#', '//', $remote ) ],
      [ 'label' => 'Head',     'value' => trim ( adminGit ( [ 'log', '-1', '--format=%H' ] ) [1] ) ]
    ];

    foreach ( explode ( "\n", adminGit ( [ 'status', '--porcelain', '--untracked-files=normal' ] ) [1] ) as $line )
      if ( strlen ( $line ) > 3 )
        $changeRows [] = [ 'state' => trim ( substr ( $line, 0, 2 ) ), 'file' => substr ( $line, 3 ),
                           'tone' => match ( trim ( substr ( $line, 0, 2 ) ) ) { '??' => 'info', 'D' => 'bad', 'A' => 'ok', default => 'warn' } ];

    foreach ( explode ( "\n", adminGit ( [ 'log', '-40', '--format=%h%x1f%ct%x1f%an%x1f%s' ] ) [1] ) as $line )
      if ( substr_count ( $line, "\x1f" ) == 3 ) {
        [ $hash, $time, $author, $subject ] = explode ( "\x1f", $line );
        $commitRows [] = [ 'hash' => $hash, 'when' => adminWhen ( $time ), 'ago' => adminAgo ( $time ), 'author' => $author,
                           'subject' => mb_strimwidth ( $subject, 0, 160, '...' ) ];
      }

  }

  $changeCount = count ( $changeRows );

?>
