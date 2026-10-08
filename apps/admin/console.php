<?php

  // The pad command from the browser: a command of a fixed list, for an application, run in
  // a child process as the web server's user (adminRun); its output shown as the command
  // line shows it. Every command is a post - several change things - and every run is kept
  // in the history, DATA/admin/history.json, with who ran it.

  global $adminCommands;

  $title    = 'Console';
  $app      = adminAppAsked ();
  $command  = adminGet ( 'command', adminField ( 'command' ) );
  $pageName = adminGet ( 'page', adminField ( 'page' ) );
  $enabled  = $adminCommands ? 1 : 0;

  // key => [ label, arguments ({app} and {page} filled in), whether it changes something,
  //          whether it needs an application ]

  $commands = [
    'lint'            => [ 'Lint - every page under the strict check',      [ 'lint', '{app}' ],                 0, 1 ],
    'test'            => [ 'Test - the application\'s own _tests/',          [ 'test', '{app}' ],                 0, 1 ],
    'render'          => [ 'Render one page',                                [ 'render', '{app}', '{page}' ],     0, 1 ],
    'migrate-status'  => [ 'Migrations - status',                            [ 'migrate', '{app}', '--status' ],  0, 1 ],
    'migrate-pretend' => [ 'Migrations - show the pending SQL',              [ 'migrate', '{app}', '--pretend' ], 0, 1 ],
    'migrate'         => [ 'Migrations - run the pending ones',              [ 'migrate', '{app}' ],              1, 1 ],
    'rollback'        => [ 'Migrations - roll back the last batch',          [ 'migrate', '{app}', '--rollback' ],1, 1 ],
    'seed'            => [ 'Seed - run the seeders',                         [ 'seed', '{app}' ],                 1, 1 ],
    'queue'           => [ 'Queue - sizes',                                  [ 'queue', '{app}' ],                0, 1 ],
    'queue-failed'    => [ 'Queue - failed jobs',                            [ 'queue', '{app}', '--failed' ],    0, 1 ],
    'queue-retry'     => [ 'Queue - retry every failed job',                 [ 'queue', '{app}', '--retry=all' ], 1, 1 ],
    'queue-flush'     => [ 'Queue - remove the failed jobs',                 [ 'queue', '{app}', '--flush' ],     1, 1 ],
    'work'            => [ 'Queue - work the due jobs once',                 [ 'work', '{app}', '--once' ],       1, 1 ],
    'schedule-list'   => [ 'Schedule - list the entries',                    [ 'schedule', '{app}', '--list' ],   0, 1 ],
    'schedule'        => [ 'Schedule - run what is due now',                 [ 'schedule', '{app}' ],             1, 1 ],
    'types'           => [ 'TypeScript types of the pages',                  [ 'types', '{app}' ],                0, 1 ],
    'down'            => [ 'Maintenance - the applications that are down',   [ 'down' ],                          0, 0 ],
    'help'            => [ 'Help - every pad command',                       [ 'help' ],                          0, 0 ]
  ];

  if ( ! isset ( $commands [$command] ) )
    $command = 'lint';

  $commandRows = [];

  foreach ( $commands as $key => [ $label, , $writes ] )
    $commandRows [] = [ 'key' => $key, 'label' => $label . ( $writes ? ' *' : '' ), 'selected' => $key === $command ? 1 : 0 ];

  $appRows = [];

  foreach ( adminApps () as $name => $one )
    if ( $name !== '_common' )
      $appRows [] = [ 'name' => $name, 'selected' => $name === $app ? 1 : 0 ];

  $ran      = 0;
  $output   = '';
  $exitCode = 0;
  $ms       = 0;
  $line     = '';
  $runError = '';

  if ( adminPost () and $enabled ) {

    [ $label, $args, $writes, $needsApp ] = $commands [$command];

    if ( $needsApp and $app === '' )
      $runError = 'Choose an application.';
    elseif ( $command == 'render' and ! preg_match ( '#^[A-Za-z0-9_\-\[\]]+(/[A-Za-z0-9_\-\[\]]+)*$#D', $pageName ) )
      $runError = 'Name the page to render, like index or orders/list.';
    else {

      $args = array_map ( fn ( $one ) => str_replace ( [ '{app}', '{page}' ], [ $app, $pageName ], $one ), $args );
      $line = 'pad ' . implode ( ' ', $args );

      [ $exitCode, $output, $ms ] = adminRun ( $args );

      $ran = 1;

      adminHistoryAdd ( $line, $exitCode, $ms );

    }

  }

  $isRender = $command === 'render' ? 1 : 0;
  $history  = adminHistory ();
  $histCount = count ( $history );

?>
