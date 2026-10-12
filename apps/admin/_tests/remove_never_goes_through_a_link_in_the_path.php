<?php

  // A directory of DATA/cache that is itself a link - to DATA/admin, DATA/keys: the storage
  // page offered it as a part to empty, and emptying it deleted what the link points to.

  $viaRoot = DATA . 'adminViaTest' . getmypid ();

  mkdir ( "$viaRoot/cache", 0777, TRUE );
  mkdir ( "$viaRoot/victim" );
  file_put_contents ( "$viaRoot/victim/users.json", 'keep' );
  symlink ( '../victim', "$viaRoot/cache/app" );

  $viaCount = 0;

  foreach ( scandir ( "$viaRoot/cache/app" ) as $viaItem )
    if ( $viaItem !== '.' and $viaItem !== '..' )
      $viaCount += adminRemove ( "$viaRoot/cache/app/$viaItem" );

  $viaKept = is_file ( "$viaRoot/victim/users.json" ) ? 'kept' : 'gone';

  @unlink ( "$viaRoot/cache/app" );
  adminRemove ( $viaRoot );

  $viaSaid = "removed through the link: $viaCount, users.json $viaKept";

?>
