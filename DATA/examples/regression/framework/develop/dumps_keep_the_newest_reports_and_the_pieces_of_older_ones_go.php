<?php

  // $padErrorKeep keeps the newest reports and deletes the rest - counting a report by its
  // stack.html. A concurrent prune that deleted a report while its request was still writing
  // left the rest of its files in a directory made again, without one: never counted, never
  // deleted. 200 errors at 16 at a time with $padErrorKeep at 20 left 64 directories.

  $pruneApp  = 'zzPruneCase' . padRandomString ( 6 );
  $pruneRoot = DATA . "dumps/$pruneApp/page/";

  foreach ( [ 'oldest' => [ 'stack.html' ], 'piece' => [ 'globals.html' ],
              'newer'  => [ 'stack.html' ], 'newest' => [ 'stack.html' ] ] as $pruneDir => $pruneFiles ) {
    mkdir ( $pruneRoot . $pruneDir, 0700, TRUE );
    foreach ( $pruneFiles as $pruneFile )
      file_put_contents ( $pruneRoot . "$pruneDir/$pruneFile", 'x' );
  }

  foreach ( [ 'oldest' => 400, 'piece' => 300, 'newer' => 200, 'newest' => 100 ] as $pruneDir => $pruneAge )
    touch ( $pruneRoot . $pruneDir, time () - $pruneAge );

  [ $pruneKeepApp, $pruneKeepKeep ] = [ $padApp, $padErrorKeep ];
  [ $padApp, $padErrorKeep ]        = [ $pruneApp, 2 ];

  padDumpPrune ();

  [ $padApp, $padErrorKeep ] = [ $pruneKeepApp, $pruneKeepKeep ];

  $pruneLeft = array_map ( 'basename', glob ( $pruneRoot . '*' ) );
  sort ( $pruneLeft );
  $pruneLeft = implode ( ' ', $pruneLeft );

  padDeleteDataDir ( DATA . "dumps/$pruneApp" );

?>
