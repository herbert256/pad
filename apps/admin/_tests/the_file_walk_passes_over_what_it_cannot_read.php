<?php

  // A file and a directory this process may not read - a server whose web user is not the
  // owner of every file: the walk failed, and with it the search of every application.

  $lockRoot = DATA . 'adminLockTest' . getmypid ();

  mkdir ( "$lockRoot/app/closed", 0777, TRUE );
  file_put_contents ( "$lockRoot/app/page.pad", 'page' );
  file_put_contents ( "$lockRoot/app/locked.php", 'locked' );
  file_put_contents ( "$lockRoot/app/closed/inside.pad", 'inside' );
  chmod ( "$lockRoot/app/locked.php", 0 );
  chmod ( "$lockRoot/app/closed", 0 );

  $lockFiles = implode ( ', ', array_map ( fn ( $f ) => substr ( $f, strlen ( "$lockRoot/app/" ) ), adminFilesIn ( "$lockRoot/app" ) ) );

  chmod ( "$lockRoot/app/locked.php", 0644 );
  chmod ( "$lockRoot/app/closed", 0755 );
  adminRemove ( $lockRoot );

?>
