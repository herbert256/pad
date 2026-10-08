<?php

  // What DATA/ holds - the engine's and the applications' runtime data - directory by
  // directory, with what each is for, and emptying the ones that are only caches and
  // leftovers. What an application relies on - its queue, its keys, the users of this
  // console and of the editor, the harvested reference - is never offered for emptying.

  $title = 'Storage';

  $known = [
    'cache'       => [ 'the page cache, fragment cache (fragments/), remote data (curl/) and application caches (app/)', 1 ],
    'dumps'       => [ 'error reports of the dump error action and the try guards', 1 ],
    'trace'       => [ 'traces of $padInfo = \'trace\'', 1 ],
    'track'       => [ 'the track info mode', 1 ],
    'coverage'    => [ 'template coverage runs', 1 ],
    'replay'      => [ 'recorded traffic for develop/?replay', 1 ],
    'samples'     => [ 'captured designer sample data', 1 ],
    'serve'       => [ 'the document roots pad serve --mount makes', 0 ],
    'poll'        => [ 'the votes of {poll}', 0 ],
    'logs'        => [ 'padLog - see Logs', 0 ],
    'mail'        => [ 'the outbox of the file mail transport - see Mail outbox', 0 ],
    'queue'       => [ 'queued and failed jobs - see Queues', 0 ],
    'schedule'    => [ 'when each scheduled entry ran last', 0 ],
    'maintenance' => [ 'the applications that are down', 0 ],
    'migrate'     => [ 'migration locks', 0 ],
    'keys'        => [ 'the encryption keys of the applications', 0 ],
    'uploads'     => [ 'files padUpload stored', 0 ],
    'admin'       => [ 'this console: its users and history', 0 ],
    'edit'        => [ 'the editor: its users, file history and trash', 0 ],
    'reference'   => [ 'the harvested reference - develop builds it', 0 ],
    'examples'    => [ 'the harvested examples - develop builds it', 0 ],
    'suites'      => [ 'the results of the regression suites', 0 ]
  ];

  if ( adminPost () ) {

    $dir = adminRelative ( adminField ( 'dir' ) );
    $top = explode ( '/', $dir ) [0];

    if ( $dir === '' or ! ( $known [$top] [1] ?? 0 ) or ! is_dir ( DATA . $dir ) )
      adminDone ( 'That directory is not one to empty from here.', 'storage', [], 'error' );

    $count = 0;

    foreach ( scandir ( DATA . $dir ) ?: [] as $item )
      if ( $item !== '.' and $item !== '..' )
        $count += adminRemove ( DATA . "$dir/$item" );

    adminDone ( "DATA/$dir is empty - $count files removed.", 'storage' );

  }

  $dirRows = [];
  $total   = 0;
  $totalFiles = 0;

  foreach ( glob ( DATA . '*', GLOB_ONLYDIR ) ?: [] as $path ) {

    $name = basename ( $path );

    [ $bytes, $files ] = adminDirSize ( $path );

    $total      += $bytes;
    $totalFiles += $files;

    $dirRows [] = [ 'dir' => $name, 'bytes' => $bytes, 'size' => adminBytes ( $bytes ), 'files' => $files,
                    'what' => $known [$name] [0] ?? '', 'clear' => ( $known [$name] [1] ?? 0 ) and $files ? 1 : 0,
                    'mb' => round ( $bytes / 1048576, 2 ) ];

    // The cache's own parts, each to be emptied apart.

    if ( $name == 'cache' )
      foreach ( glob ( "$path/*", GLOB_ONLYDIR ) ?: [] as $sub ) {
        [ $subBytes, $subFiles ] = adminDirSize ( $sub );
        $dirRows [] = [ 'dir' => 'cache/' . basename ( $sub ), 'bytes' => $subBytes, 'size' => adminBytes ( $subBytes ),
                        'files' => $subFiles, 'what' => '', 'clear' => $subFiles ? 1 : 0, 'mb' => round ( $subBytes / 1048576, 2 ) ];
      }

  }

  usort ( $dirRows, fn ( $a, $b ) => strcmp ( $a ['dir'], $b ['dir'] ) );

  $chartRows = array_values ( array_filter ( $dirRows, fn ( $one ) => ! str_contains ( $one ['dir'], '/' ) and $one ['bytes'] > 0 ) );
  $hasChart  = count ( $chartRows ) ? 1 : 0;
  $totalText = adminBytes ( $total ) . " in $totalFiles files";
  $dataPath  = rtrim ( DATA, '/' );

?>
