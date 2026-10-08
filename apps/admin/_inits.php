<?php

  // The frame of every page: who is logged in, and the menu with the page it is on.

  $adminIn   = ( PHP_SAPI !== 'cli' and adminUserValid ( $adminUser, $adminStamp ) ) ? 1 : 0;
  $adminWho  = $adminIn ? $adminUser : '';
  $adminHost = php_uname ( 'n' );
  $title     = 'Dashboard';

  $adminSection = explode ( '/', $padPage ) [0];

  $adminMenu = [];

  foreach ( [
    'Overview'    => [ 'index' => [ 'Dashboard', 'home' ],     'apps' => [ 'Applications', 'grid' ],
                       'files' => [ 'Files', 'folder' ],        'search' => [ 'Search', 'search' ] ],
    'Operations'  => [ 'console' => [ 'Console', 'terminal' ],  'maintenance' => [ 'Maintenance', 'power' ],
                       'queues' => [ 'Queues', 'list' ],        'schedule' => [ 'Schedule', 'calendar' ] ],
    'Diagnostics' => [ 'logs' => [ 'Logs', 'file' ],            'dumps' => [ 'Error dumps', 'alert' ],
                       'mail' => [ 'Mail outbox', 'mail' ] ],
    'System'      => [ 'storage' => [ 'Storage', 'database' ],  'php' => [ 'PHP', 'code' ],
                       'settings' => [ 'Settings', 'settings' ], 'git' => [ 'Git', 'link' ],
                       'users' => [ 'Users', 'users' ] ]
  ] as $adminGroup => $adminItems ) {

    $adminMenu [] = [ 'head' => $adminGroup, 'page' => '', 'label' => '', 'icon' => '', 'current' => 0 ];

    foreach ( $adminItems as $adminItem => [ $adminLabel, $adminIcon ] )
      $adminMenu [] = [ 'head' => '', 'page' => $adminItem, 'label' => $adminLabel, 'icon' => $adminIcon,
                        'current' => ( $adminSection == $adminItem
                                       or ( $adminItem == 'apps' and $adminSection == 'application' ) ) ? 1 : 0 ];

  }

?>
