<?php

  // Every application, with what it has: the successor of the apps application. The test
  // applications of the regression family are many and alike; they are left out unless
  // asked for (all=1), as the old listing did, regression/main aside.

  $title   = 'Applications';
  $showAll = adminGet ( 'all' ) === '1' ? 1 : 0;

  $appRows = [];
  $hidden  = 0;

  foreach ( adminApps () as $name => $one ) {

    if ( ! $showAll and str_starts_with ( $name, 'regression/' ) and $name !== 'regression/main' ) {
      $hidden++;
      continue;
    }

    $dir    = $one ['dir'];
    $badges = [];

    foreach ( [ '_guard.php' => 'guard', '.env' => 'env', '_config' => 'config', '_migrations' => 'migrations',
                '_jobs' => 'jobs', '_schedule.php' => 'schedule', '_tests' => 'tests', '_lang' => 'i18n',
                '_mail' => 'mail', '_errors' => 'error pages', '_tags' => 'tags' ] as $part => $badge )
      if ( file_exists ( $dir . $part ) )
        $badges [] = $badge;

    $down = padMaintenanceRead ( $name ) ? 1 : 0;

    $appRows [] = [
      'name'        => $name,
      'link'        => $one ['link'],
      'description' => $one ['description'],
      'pages'       => $name === '_common' ? '' : count ( adminAppPages ( $name ) ),
      'badgeRows'   => array_map ( fn ( $b ) => [ 'badge' => $b ], $badges ),
      'down'        => $down
    ];

  }

  $appCount = count ( $appRows );

?>
