<?php

  // A directory with a link in it to a directory outside: the search walked it and showed
  // the lines of the files there, which the files page refuses to show.

  $walkRoot = DATA . 'adminWalkTest' . getmypid ();

  mkdir ( "$walkRoot/app/sub", 0777, TRUE );
  mkdir ( "$walkRoot/outside" );
  file_put_contents ( "$walkRoot/app/page.pad", 'page' );
  file_put_contents ( "$walkRoot/app/sub/part.pad", 'part' );
  file_put_contents ( "$walkRoot/outside/secret.txt", 'secret' );
  symlink ( '../outside', "$walkRoot/app/linked" );
  symlink ( '../outside/secret.txt', "$walkRoot/app/secret.txt" );

  $walkFiles = implode ( ', ', array_map ( fn ( $f ) => substr ( $f, strlen ( "$walkRoot/app/" ) ), adminFilesIn ( "$walkRoot/app" ) ) );

  adminRemove ( $walkRoot );

?>
