<?php

  // The session helpers over real requests, with the session cookie carried along as a
  // browser carries it: a read without the cookie sends no session cookie back, a put
  // starts the session, the next request reads it and pulls a value out - gone on the one
  // after - and a new id after a "login" keeps the data and replaces the cookie.

  $sessionUrl = $padGoExt . 'helpers/requests_session_';
  $sessionAsk = fn ( $page, $jar = [] ) => padCurl ( [ 'url' => $sessionUrl . "$page&padInclude", 'cookies' => $jar ] );

  $sessionNone = $sessionAsk ( 'read' );
  $sessionPut  = $sessionAsk ( 'put' );
  $sessionJar  = [ 'PHPSESSID' => $sessionPut ['cookies'] ['PHPSESSID'] ?? '' ];
  $sessionOne  = $sessionAsk ( 'read', $sessionJar );
  $sessionTwo  = $sessionAsk ( 'read', $sessionJar );
  $sessionNew  = $sessionAsk ( 'regenerate', $sessionJar );
  $sessionId   = $sessionNew ['cookies'] ['PHPSESSID'] ?? '';
  $sessionOld  = $sessionAsk ( 'read', $sessionJar );
  $sessionNext = $sessionAsk ( 'read', [ 'PHPSESSID' => $sessionId ] );

  $sessionResult = implode ( ' | ', [
    trim ( $sessionNone ['data'] ) . ( isset ( $sessionNone ['cookies'] ['PHPSESSID'] ) ? ' cookie sent' : ' no cookie' ),
    trim ( $sessionPut  ['data'] ) . ( $sessionJar ['PHPSESSID'] !== '' ? ' cookie sent' : ' no cookie' ),
    trim ( $sessionOne  ['data'] ),
    trim ( $sessionTwo  ['data'] ),
    trim ( $sessionNew  ['data'] ) . ( ( $sessionId !== '' && $sessionId !== $sessionJar ['PHPSESSID'] ) ? ' new id' : ' same id' ),
    'old id: ' . trim ( $sessionOld  ['data'] ),
    'new id: ' . trim ( $sessionNext ['data'] ),
  ] );

?>
