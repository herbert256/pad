<?php

  // Template coverage: which templates a run of requests rendered, which of their tags ran,
  // and which branch each {if} and {case} took - so a suite run can show the parts of an
  // application no test ever reaches.
  //
  // Recording is for local requests (padLocal) and is switched on in one of three ways, the
  // name saying which run the requests are collected in:
  //
  //   $padCoverage = TRUE;                in _config/config.php - the run 'default'
  //   $padCoverage = 'suites';            the same, into the run 'suites'
  //   ?page&padCoverage=suites            one request
  //   DATA/coverage/recording             a file holding a run name: every local request of
  //                                       every application records - develop/?coverage
  //                                       starts and stops it around a suite run
  //
  // While a request runs, padCoverageFile notes each template it reads (padFileGet),
  // padCoverageTag each tag that opens a level - under its text as written, {if $n eq 1} as
  // 'if $n eq 1' - and padCoverageArm the branch an {if} or {case} took; the PHP files it
  // included are read off get_included_files at the end. padCoverageWrite, called as the
  // request ends (lib/exit.php), appends it all as one JSON line to DATA/coverage/<run>.jsonl.
  // What it collects lives in a static of padCoverageStore, because a nested pass - a
  // sandbox, a {page} - puts every pad* global back the way it found it when it is done.
  //
  // The report side matches that record against the templates' own source: padCoverageItems
  // lists the tags and branches a template holds, padCoverageMark counts how often each ran,
  // padCoverageReport does so for every template a run touched, and padCoverageHtml shows a
  // template with what never ran marked. A tag is matched on its text; one with a tag inside
  // its parameters, {echo {$x}}, was rendered with that inner tag already replaced, and is
  // matched with the inner part as a wildcard.

  function padCoverageRun () {

    global $padApp, $padCoverage;

    if ( $padApp == 'develop' or ! padLocal () )
      return '';

    if ( padSelfSwitch ( 'padCoverage' ) )
      return padCoverageName ( $_REQUEST ['padCoverage'] );

    if ( $padCoverage ?? FALSE )
      return padCoverageName ( is_string ( $padCoverage ) ? $padCoverage : '' );

    if ( file_exists ( DATA . 'coverage/recording' ) )
      return padCoverageName ( file_get_contents ( DATA . 'coverage/recording' ) );

    return '';

  }

  function padCoverageName ( $name ) {

    $name = preg_replace ( '/[^A-Za-z0-9_-]/', '', (string) $name );

    return ( $name === '' ) ? 'default' : substr ( $name, 0, 40 );

  }

  function &padCoverageStore () {

    static $store = [ 'files' => [], 'tags' => [], 'arms' => [] ];

    return $store;

  }

  function padCoverageKey ( $text ) {

    return trim ( preg_replace ( '/\s+/', ' ', trim ( (string) $text, " \t\n\r~" ) ) );

  }

  function padCoverageFile ( $file ) {

    if ( ! str_starts_with ( $file, APPS ) )
      return;

    if ( ! str_ends_with ( $file, '.pad' ) and ! str_ends_with ( $file, '.html' ) )
      return;

    $store = &padCoverageStore ();

    $store ['files'] [ substr ( $file, strlen ( APPS ) ) ] = TRUE;

  }

  // A level's outcome: its content rendered (hit), its @else@ branch (else), or nothing at
  // all because the tag answered NULL (null).

  function padCoverageTag () {

    global $pad, $padElse, $padNull, $padOrg;

    $key = padCoverageKey ( $padOrg [$pad] ?? '' );

    if ( $key === '' )
      return;

    $how = ( $padNull [$pad] ?? FALSE ) ? 'null' : ( ( $padElse [$pad] ?? FALSE ) ? 'else' : 'hit' );

    $store = &padCoverageStore ();

    $store ['tags'] [$key] [$how] = ( $store ['tags'] [$key] [$how] ?? 0 ) + 1;

  }

  // The branch an {if} or a {case} took: 'if', 'elseif <condition>', 'when <values>',
  // 'else', or 'none' when no branch held and there was no {else} to fall to.

  function padCoverageArm ( $arm ) {

    global $pad, $padOrg;

    $key = padCoverageKey ( $padOrg [$pad] ?? '' );
    $arm = padCoverageKey ( $arm );

    $store = &padCoverageStore ();

    $store ['arms'] [$key] [$arm] = ( $store ['arms'] [$key] [$arm] ?? 0 ) + 1;

  }

  function padCoverageWrite ( $stop ) {

    global $padApp, $padCoverageRun, $padStartPage;

    $store = &padCoverageStore ();

    foreach ( get_included_files () as $file )
      if ( str_starts_with ( $file, APPS ) )
        $store ['files'] [ substr ( $file, strlen ( APPS ) ) ] = TRUE;

    padFilePut ( "coverage/$padCoverageRun.jsonl", json_encode ( [
      'app'    => $padApp,
      'page'   => $padStartPage ?? '',
      'status' => (string) $stop,
      'files'  => array_keys ( $store ['files'] ),
      'tags'   => (object) $store ['tags'],
      'arms'   => (object) $store ['arms']
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ), 1 );

  }

  // ---- the report ---------------------------------------------------------------------

  // The runs there are, newest first.

  function padCoverageRuns () {

    $runs = [];

    foreach ( glob ( DATA . 'coverage/*.jsonl' ) ?: [] as $file )
      $runs [ basename ( $file, '.jsonl' ) ] = filemtime ( $file );

    arsort ( $runs );

    return array_keys ( $runs );

  }

  // A run read back: per file, how many requests read it and every tag and branch those
  // requests ran. A tag is credited to every file its request read - the record knows what
  // ran, not where it stood - and padCoverageMark only ever asks about tags a file holds.

  function padCoverageRead ( $run ) {

    $files = [];
    $count = 0;

    $file = DATA . 'coverage/' . padCoverageName ( $run ) . '.jsonl';

    if ( ! file_exists ( $file ) )
      return [ 'requests' => 0, 'files' => [] ];

    $handle = fopen ( $file, 'r' );

    while ( ( $line = fgets ( $handle ) ) !== FALSE ) {

      $one = json_decode ( $line, TRUE );

      if ( ! is_array ( $one ) )
        continue;

      $count++;

      foreach ( $one ['files'] ?? [] as $name ) {

        $files [$name] ['requests'] = ( $files [$name] ['requests'] ?? 0 ) + 1;

        foreach ( $one ['tags'] ?? [] as $key => $how )
          $files [$name] ['tags'] [$key] = ( $files [$name] ['tags'] [$key] ?? 0 ) + array_sum ( $how );

        foreach ( $one ['arms'] ?? [] as $key => $arms )
          foreach ( $arms as $arm => $times )
            $files [$name] ['arms'] [$key] [$arm] = ( $files [$name] ['arms'] [$key] [$arm] ?? 0 ) + $times;

      }

    }

    fclose ( $handle );

    ksort ( $files );

    return [ 'requests' => $count, 'files' => $files ];

  }

  // The tags and branches a template holds, in source order. Each item has the text as
  // written, its offset and length, its line, and either the tag key it is matched on
  // (kind 'tag') or the {if}/{case} it belongs to and the branch it is (kind 'arm'). An
  // {if} is its branches, the first of them 'if'; one without an {else} gets a 'none' item
  // on its {/if}, for the run in which no branch held. Fields - {$x} and the other sigils -
  // closing tags and comments are no items; what stands inside {ignore} is text.

  function padCoverageItems ( $source ) {

    $items = [];
    $stack = [];
    $len   = strlen ( $source );
    $pos   = 0;

    while ( ( $pos = strpos ( $source, '{', $pos ) ) !== FALSE ) {

      $end = padCoverageClose ( $source, $pos );

      if ( $end === FALSE ) {
        $pos++;
        continue;
      }

      $text = substr ( $source, $pos, $end - $pos + 1 );
      $tag  = trim ( substr ( $text, 1, -1 ), " \t\n\r~" );
      $item = [ 'pos' => $pos, 'len' => $end - $pos + 1, 'line' => substr_count ( $source, "\n", 0, $pos ) + 1, 'text' => $text ];
      $next = $end + 1;

      if ( $tag === '' or ! preg_match ( '/^\/?[A-Za-z]/', $tag ) ) {
        $pos = ( $tag !== '' and str_contains ( '$!#&?^', $tag [0] ) ) ? $next : $pos + 1;
        continue;
      }

      $name = strtolower ( preg_split ( '/[\s|]+/', $tag, 2 ) [0] );
      $key  = padCoverageKey ( $tag );

      if ( $name == 'ignore' ) {
        $close = stripos ( $source, '{/ignore', $next );
        $pos   = ( $close === FALSE ) ? $len : $close;
        continue;
      }

      if ( $name == 'if' or $name == 'case' ) {
        $stack [] = [ 'name' => $name, 'key' => $key, 'else' => FALSE ];
        $items [] = $item + ( $name == 'if' ? [ 'kind' => 'arm', 'owner' => $key, 'arm' => 'if' ]
                                            : [ 'kind' => 'tag', 'key'   => $key               ] );
      } elseif ( ( $name == '/if' or $name == '/case' ) and $stack and end ( $stack ) ['name'] == substr ( $name, 1 ) ) {
        $open = array_pop ( $stack );
        if ( ! $open ['else'] )
          $items [] = $item + [ 'kind' => 'arm', 'owner' => $open ['key'], 'arm' => 'none' ];
      } elseif ( $name [0] == '/' ) {
      } elseif ( in_array ( $name, [ 'else', 'elseif', 'when' ] ) and $stack ) {
        if ( $name == 'else' )
          $stack [ array_key_last ( $stack ) ] ['else'] = TRUE;
        $items [] = $item + [ 'kind' => 'arm', 'owner' => end ( $stack ) ['key'], 'arm' => $key ];
      } else
        $items [] = $item + [ 'kind' => 'tag', 'key' => $key ];

      $pos = $next;

    }

    return $items;

  }

  // The } that closes the { at $pos, by brace depth - {echo {$x}} closes at its last brace.

  function padCoverageClose ( $source, $pos ) {

    $depth = 0;
    $len   = strlen ( $source );

    for ( $i = $pos; $i < $len; $i++ )
      if ( $source [$i] == '{' )
        $depth++;
      elseif ( $source [$i] == '}' and ! --$depth )
        return $i;

    return FALSE;

  }

  // How often each item ran, given what a run recorded for the file: a tag by its key, a
  // branch under the key of its {if} or {case}.

  function padCoverageMark ( $items, $tags, $arms ) {

    foreach ( $items as $i => $item )
      if ( $item ['kind'] == 'tag' )
        $items [$i] ['hit'] = padCoverageFind ( $item ['key'], $tags );
      else
        $items [$i] ['hit'] = padCoverageFind ( $item ['arm'], padCoverageArms ( $item ['owner'], $arms ) );

    return $items;

  }

  function padCoverageArms ( $owner, $arms ) {

    if ( isset ( $arms [$owner] ) or ! str_contains ( $owner, '{' ) )
      return $arms [$owner] ?? [];

    $merged = [];

    foreach ( $arms as $key => $list )
      if ( preg_match ( padCoverageWild ( $owner ), $key ) )
        foreach ( $list as $arm => $times )
          $merged [$arm] = ( $merged [$arm] ?? 0 ) + $times;

    return $merged;

  }

  function padCoverageFind ( $key, $counts ) {

    if ( isset ( $counts [$key] ) )
      return $counts [$key];

    if ( ! str_contains ( $key, '{' ) )
      return 0;

    $hit     = 0;
    $pattern = padCoverageWild ( $key );

    foreach ( $counts as $have => $times )
      if ( preg_match ( $pattern, $have ) )
        $hit += $times;

    return $hit;

  }

  // A key with tags inside it as a pattern: the inner {...} parts match anything.

  function padCoverageWild ( $key ) {

    $parts = preg_split ( '/\{[^{}]*\}/', $key );

    return '/^' . implode ( '.*?', array_map ( fn ( $part ) => preg_quote ( $part, '/' ), $parts ) ) . '$/s';

  }

  // Every template the run read, with the share of its tags and branches that ran; and, per
  // application the run touched, the templates and PHP files it never read - pages, includes,
  // tags, functions, callbacks, manual fragments no test exercises.

  function padCoverageReport ( $run ) {

    $read  = padCoverageRead ( $run );
    $files = [];
    $apps  = [];

    foreach ( $read ['files'] as $name => $info ) {

      $apps [ explode ( '/', $name ) [0] ] = TRUE;

      if ( ! str_ends_with ( $name, '.pad' ) and ! str_ends_with ( $name, '.html' ) )
        continue;

      $items = padCoverageMark ( padCoverageItems ( padFileGet ( APPS . $name ) ), $info ['tags'] ?? [], $info ['arms'] ?? [] );
      $hit   = count ( array_filter ( $items, fn ( $item ) => $item ['hit'] > 0 ) );

      $files [] = [
        'file'     => $name,
        'requests' => $info ['requests'],
        'items'    => count ( $items ),
        'covered'  => $hit,
        'percent'  => count ( $items ) ? (int) floor ( 100 * $hit / count ( $items ) ) : 100
      ];

    }

    $unread = [];

    foreach ( array_keys ( $apps ) as $app ) {

      if ( str_starts_with ( $app, '_' ) or ! is_dir ( APPS . $app ) )
        continue;

      $iterator = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( APPS . $app, FilesystemIterator::SKIP_DOTS ) );

      foreach ( $iterator as $one ) {

        $name = substr ( padCorrectPath ( $one->getPathname () ), strlen ( APPS ) );

        if ( ! preg_match ( '/\.(pad|html|php)$/', $name ) or isset ( $read ['files'] [$name] ) )
          continue;

        if ( preg_match ( '#/_(config|data|lang)/#', $name ) )
          continue;

        $unread [] = $name;

      }

    }

    sort ( $unread );

    return [ 'requests' => $read ['requests'], 'files' => $files, 'unread' => $unread ];

  }

  // A template's source as HTML, every item wrapped: covered ones in <span class="pad-cover-hit">,
  // the ones that never ran in <mark class="pad-cover-miss">, each titled with how often it
  // ran - a 'none' item says "no branch held".

  function padCoverageHtml ( $source, $items ) {

    $html = '';
    $at   = 0;

    foreach ( $items as $item ) {

      if ( $item ['pos'] < $at )
        continue;

      $html .= htmlspecialchars ( substr ( $source, $at, $item ['pos'] - $at ) );

      $what  = ( ( $item ['arm'] ?? '' ) == 'none' ) ? 'no branch held' : 'ran';
      $title = $what . ' ' . $item ['hit'] . ( $item ['hit'] == 1 ? ' time' : ' times' );
      $text  = htmlspecialchars ( $item ['text'] );

      $html .= $item ['hit']
             ? "<span class=\"pad-cover-hit\" title=\"$title\">$text</span>"
             : "<mark class=\"pad-cover-miss\" title=\"$title\">$text</mark>";

      $at = $item ['pos'] + $item ['len'];

    }

    return $html . htmlspecialchars ( substr ( $source, $at ) );

  }

?>
