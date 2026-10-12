<?php

  // A directory the web server may not read inside an application, and one directly in
  // apps/: the application page, the list of applications and so every page of the console
  // failed. The walks are tried on a scratch tree under DATA/, reached from APPS by ../.

  $walkTree = 'adminWalkTest' . getmypid ();
  $walkRoot = DATA . $walkTree;

  mkdir ( "$walkRoot/closed", 0777, TRUE );
  file_put_contents ( "$walkRoot/index.pad", 'x' );
  chmod ( "$walkRoot/closed", 0 );

  $walkPages = implode ( ', ', array_keys ( adminAppPages ( "../DATA/$walkTree" ) ) );
  $walkApps  = [];

  adminAppsWalk ( "../DATA/$walkTree/", $walkApps, 0 );

  chmod ( "$walkRoot/closed", 0755 );
  adminRemove ( $walkRoot );

  $walkSaid = "pages: $walkPages, applications found: " . count ( $walkApps );

?>
