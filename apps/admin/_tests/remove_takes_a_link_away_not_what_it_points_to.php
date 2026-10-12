<?php

  // Emptying a directory of DATA/ that holds a link to another one - DATA/keys, a users
  // file: the link goes, what it points to stays.

  $removeRoot = DATA . 'adminRemoveTest' . getmypid ();

  mkdir ( "$removeRoot/cache", 0777, TRUE );
  mkdir ( "$removeRoot/precious" );
  file_put_contents ( "$removeRoot/precious/users.json", 'keep' );
  file_put_contents ( "$removeRoot/cache/old.txt", 'old' );
  symlink ( '../precious', "$removeRoot/cache/link" );

  $removeCount = 0;

  foreach ( scandir ( "$removeRoot/cache" ) as $removeItem )
    if ( $removeItem !== '.' and $removeItem !== '..' )
      $removeCount += adminRemove ( "$removeRoot/cache/$removeItem" );

  $removeKept = is_file ( "$removeRoot/precious/users.json" ) ? 'kept' : 'gone';
  $removeLink = is_link ( "$removeRoot/cache/link" ) ? 'still there' : 'gone';
  $removeLeft = count ( scandir ( "$removeRoot/cache" ) ) - 2;

  adminRemove ( $removeRoot );

  $removeClean = file_exists ( $removeRoot ) ? 'left behind' : 'cleaned up';

?>
