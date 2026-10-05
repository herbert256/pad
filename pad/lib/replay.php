<?php

  // Replay real traffic as tests: GET requests are recorded with the answer they got, and
  // replayed later against the code as it stands - a framework or template change that alters
  // what any recorded page answers shows up before release.
  //
  //   $padRecord = TRUE;          in _config/config.php - every qualifying request is recorded
  //                               in the store 'default'; a name instead of TRUE picks the store
  //   ?page&padRecord=name        one request, asked for by this machine itself
  //
  // A request qualifies when it is a GET, its output goes to the web, it answered 200, and it
  // brought no cookie but PAD's own two ids: a request carrying a session, a login or any other
  // state cannot be replayed faithfully - the replay comes without cookies - so it is not
  // recorded at all. One URL is one case: a later answer to the same app and query string
  // replaces the earlier one. A case is DATA/replay/<store>/<md5>.json, holding the app, the
  // query, the host it was recorded under, the status, the content type and the body.
  //
  // A replay fetches each case again with padReplay added - a switch only this machine can
  // throw, which also keeps the replay itself from being recorded - and compares status and
  // body, the host the answer was recorded under read as the host it is replayed on. While
  // padReplaying() is true a request does not write: db() refuses every statement that would
  // change the application's database and answers 0, and padFilePut refuses a write the
  // application's own code asks for. PHP's own file and mail functions are not intercepted.
  //
  // padReplayStore     the store this request is recorded in, '' for none
  // padReplayRecord    records the finished request (exits/output.php)
  // padReplaying       whether this request is a replay
  // padReplayWrites    whether an SQL statement would change the database
  // padReplayCases     the cases of a store
  // padReplayRun       replays every case of a store and says, per case, what changed
  // padReplaySave / padReplayDelete / padReplayStores    keeping the store

  function padReplayStore () {

    global $padOutputType, $padRecord;

    if ( padReplaying ()                                ) return '';
    if ( ( $_SERVER ['REQUEST_METHOD'] ?? '' ) != 'GET' ) return '';
    if ( ( $padOutputType ?? 'web' ) != 'web'           ) return '';

    foreach ( array_keys ( $_COOKIE ) as $cookie )
      if ( $cookie != 'padSesID' and $cookie != 'padReqID' )
        return '';

    if ( padSelfSwitch ( 'padRecord' ) )
      return padCoverageName ( $_REQUEST ['padRecord'] );

    if ( $padRecord ?? FALSE )
      return padCoverageName ( is_string ( $padRecord ) ? $padRecord : '' );

    return '';

  }

  function padReplayRecord ( $stop ) {

    global $padApp, $padCacheServerGzip, $padCacheStop, $padContentType, $padHost, $padOutput;

    if ( $stop != 200 )
      return;

    $store = padReplayStore ();

    if ( $store === '' )
      return;

    $query = padReplayQuery ( $_SERVER ['QUERY_STRING'] ?? '' );
    $body  = ( $padCacheStop == 200 and $padCacheServerGzip ) ? padUnzip ( $padOutput ) : $padOutput;

    padReplaySave ( $store, [
      'app'    => $padApp,
      'query'  => $query,
      'host'   => $padHost,
      'status' => 200,
      'type'   => $padContentType,
      'when'   => date ( 'Y-m-d H:i:s' ),
      'body'   => $body
    ] );

  }

  // The query string a case is replayed with: the padRecord switch that asked for the
  // recording is taken out, everything else stays as the visitor sent it.

  function padReplayQuery ( $query ) {

    $keep = [];

    foreach ( explode ( '&', $query ) as $part )
      if ( $part !== '' and explode ( '=', $part, 2 ) [0] !== 'padRecord' )
        $keep [] = $part;

    return implode ( '&', $keep );

  }

  function padReplaying () {

    static $replaying = NULL;

    return $replaying ??= padSelfSwitch ( 'padReplay' );

  }

  function padReplayWrites ( $sql ) {

    $verb = strtolower ( strtok ( ltrim ( (string) $sql, " \t\n\r(" ), " \t\n\r(" ) );

    return in_array ( $verb, [ 'insert', 'update', 'delete', 'replace', 'truncate', 'load', 'create',
                               'drop', 'alter', 'rename', 'grant', 'revoke', 'call', 'lock' ] );

  }

  // A write is the application's when the code that called padFilePut lives in an
  // application directory - the engine's own logs, caches and reports are not.

  function padReplayAppWrite () {

    foreach ( debug_backtrace ( DEBUG_BACKTRACE_IGNORE_ARGS, 3 ) as $frame )
      if ( ( $frame ['function'] ?? '' ) == 'padFilePut' )
        return str_starts_with ( padCorrectPath ( $frame ['file'] ?? '' ), APPS );

    return FALSE;

  }

  function padReplayDir ( $store ) {

    return DATA . 'replay/' . padCoverageName ( $store ) . '/';

  }

  function padReplayStores () {

    $stores = [];

    foreach ( glob ( DATA . 'replay/*', GLOB_ONLYDIR ) ?: [] as $dir )
      $stores [] = basename ( $dir );

    sort ( $stores );

    return $stores;

  }

  function padReplayCases ( $store ) {

    $cases = [];

    foreach ( glob ( padReplayDir ( $store ) . '*.json' ) ?: [] as $file ) {

      $case = json_decode ( (string) file_get_contents ( $file ), TRUE );

      if ( is_array ( $case ) )
        $cases [ basename ( $file, '.json' ) ] = $case + [ 'id' => basename ( $file, '.json' ) ];

    }

    uasort ( $cases, fn ( $a, $b ) => strcmp ( $a ['app'] . '?' . $a ['query'], $b ['app'] . '?' . $b ['query'] ) );

    return $cases;

  }

  function padReplaySave ( $store, $case ) {

    unset ( $case ['id'] );

    padFilePut ( 'replay/' . padCoverageName ( $store ) . '/' . md5 ( $case ['app'] . '?' . $case ['query'] ) . '.json', $case );

  }

  function padReplayDelete ( $store, $id = '' ) {

    $dir = padReplayDir ( $store );

    if ( $id !== '' ) {
      if ( preg_match ( '/^[0-9a-f]{32}$/', $id ) and file_exists ( "$dir$id.json" ) )
        unlink ( "$dir$id.json" );
      return;
    }

    foreach ( glob ( $dir . '*.json' ) ?: [] as $file )
      unlink ( $file );

    if ( is_dir ( $dir ) )
      @rmdir ( $dir );

  }

  // Every case fetched again - a window at a time, they are independent GETs - and judged:
  // same, or the status that changed, or the first line where the bodies part. Both ends
  // are trimmed, as the regression suites trim theirs: the client side of a fetch does.

  function padReplayRun ( $store ) {

    global $padHost;

    $cases   = padReplayCases ( $store );
    $urls    = [];
    $results = [];

    foreach ( $cases as $id => $case )
      $urls [$id] = $padHost . $case ['app'] . '/?' . ( $case ['query'] === '' ? 'index' : $case ['query'] ) . '&padReplay';

    $fetched = padCurlMulti ( $urls );

    foreach ( $cases as $id => $case ) {

      $curl   = $fetched [$id] ?? [ 'data' => '', 'result' => '999' ];
      $status = (int) $curl ['result'];
      $want   = trim ( padReplayHostless ( (string) $case ['body'], $case ['host'] ?? $padHost ) );
      $got    = trim ( padReplayHostless ( (string) $curl ['data'], $padHost ) );

      $result = [ 'id' => $id, 'app' => $case ['app'], 'query' => $case ['query'], 'when' => $case ['when'] ?? '',
                  'same' => ( $status == $case ['status'] and $want === $got ),
                  'status' => $status, 'line' => 0, 'want' => '', 'got' => '', 'answer' => (string) $curl ['data'] ];

      if ( ! $result ['same'] and $status == $case ['status'] ) {

        $wantLines = explode ( "\n", $want );
        $gotLines  = explode ( "\n", $got  );

        for ( $i = 0; $i < max ( count ( $wantLines ), count ( $gotLines ) ); $i++ )
          if ( ( $wantLines [$i] ?? NULL ) !== ( $gotLines [$i] ?? NULL ) )
            break;

        $result ['line'] = $i + 1;
        $result ['want'] = $wantLines [$i] ?? '(the answer ended)';
        $result ['got']  = $gotLines  [$i] ?? '(the answer ended)';

      }

      $results [] = $result;

    }

    return $results;

  }

  // The host an answer was rendered under, read as one name, so an answer recorded on
  // http://localhost/pad/ is the same answer replayed on http://127.0.0.1:8080/pad/.

  function padReplayHostless ( $text, $base ) {

    $host = parse_url ( (string) $base, PHP_URL_HOST ) ?? '';
    $port = parse_url ( (string) $base, PHP_URL_PORT );

    if ( $port )
      $host .= ":$port";

    if ( $host === '' )
      return $text;

    return preg_replace ( '#(//|\\\\/\\\\/)' . preg_quote ( $host, '#' ) . '(?![\w.-])#', '$1replay.host', $text );

  }

?>
