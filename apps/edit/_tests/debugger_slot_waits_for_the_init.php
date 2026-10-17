<?php

  // The Xdebug port is open to every process of this machine. A connection that says
  // nothing does not take the debugger's one slot: the request that connects after it, with
  // its init, is the one debugged - where the first connection was taken for the request at
  // once, and every request after it was let go ("another request ran on without
  // stopping") until that connection closed. A connection that never says what it is goes
  // after a few seconds. No Xdebug is needed: the requests are played by hand, as DBGp.

  $dir   = editDebugDir ( DATA . 'edit-test-dbgslot-' . getmypid () . '/' );
  $probe = stream_socket_server ( 'tcp://127.0.0.1:0' );
  $port  = (int) substr ( strrchr ( stream_socket_get_name ( $probe, FALSE ), ':' ), 1 );

  fclose ( $probe );

  $out = [];

  editDebugStart ( $dir, $port );
  editDebugAsk ( $dir, [ 'cmd' => 'breakpoints', 'list' => [ [ 'file' => '/x/apps/demo/index.php', 'line' => 3 ] ], 'first' => TRUE ] );

  $init = function ( $file ) {
    $xml = '<?xml version="1.0" encoding="iso-8859-1"?><init xmlns="urn:debugger_protocol_v1" fileuri="file://' . $file
         . '" language="PHP" idekey="padedit" protocol_version="1.0" appid="1"></init>';
    return strlen ( $xml ) . "\0" . $xml . "\0";
  };

  // the first command the debugger sends a request, by its name

  $first = function ( $socket ) {
    stream_set_timeout ( $socket, 3 );
    $got = '';
    while ( ! str_contains ( $got, "\0" ) and ( $more = fread ( $socket, 4096 ) ) !== '' and $more !== FALSE )
      $got .= $more;
    return strtok ( $got, ' ' ) ?: 'nothing';
  };

  $silent = stream_socket_client ( "tcp://127.0.0.1:$port" );

  usleep ( 200000 );

  $request = stream_socket_client ( "tcp://127.0.0.1:$port" );
  fwrite ( $request, $init ( '/x/www/demo/index.php' ) );

  $out [] = 'the request after a silent connection is sent: ' . $first ( $request );

  $state  = editDebugAsk ( $dir, [ 'cmd' => 'state', 'since' => 0 ] );
  $out [] = 'debugged: ' . ( $state ['session'] ['file'] ?? 'nothing' );

  $second = stream_socket_client ( "tcp://127.0.0.1:$port" );
  fwrite ( $second, $init ( '/x/www/shop/index.php' ) );

  $out [] = 'a second request meanwhile is sent: ' . $first ( $second );

  $state  = editDebugAsk ( $dir, [ 'cmd' => 'state', 'since' => 0 ] );
  $out [] = 'still debugged: ' . ( $state ['session'] ['file'] ?? 'nothing' );
  $out [] = 'logged: ' . implode ( ' / ', array_map ( fn ( $line ) => substr ( $line, 9 ), $state ['log'] ?? [] ) );

  stream_set_timeout ( $silent, 10 );

  $read   = fread ( $silent, 10 );
  $out [] = 'the silent connection is closed: ' . ( ( $read === '' and feof ( $silent ) ) ? 'yes' : 'no' );

  fclose ( $second );
  fclose ( $request );
  fclose ( $silent );

  editDebugStop ( $dir );
  editRemoveTree ( rtrim ( dirname ( $dir ), '/' ) );

?>
