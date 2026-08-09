<?php

  // A section's dir is engine-rooted, unless it says common: - the shared application's
  // own toolkit lives under apps/_common/, out of the engine tree's reach.

  function getReference ( $dir, $xref ) {

    if ( ! $dir )
      return [];

    $root = PAD;

    if ( str_starts_with ( $dir, 'common:' ) ) {
      $root = COMMON;
      $dir  = substr ( $dir, 7 );
    }

    $items = [];

    foreach ( scandir ( $root . $dir ) as $file ) {

      if ( $file == '.'                     ) continue;
      if ( $file == '..'                    ) continue;
      if ( str_starts_with ( $file, '_'   ) ) continue;
      if ( str_ends_with   ( $file, '.md' ) ) continue;

      $item = ( str_contains ( $file, '.') )
            ? substr ( $file, 0, strrpos ( $file, '.') )
            : $file;

      $items [$item] ['item']  = $item;
      $items [$item] ['dir']   = is_dir      ( DATA . "reference/$xref/$item"     );
      $items [$item] ['pages'] = file_exists ( DATA . "reference/$xref/$item.txt" );
      $items [$item] ['cases'] = getReferenceCased ( $item, $xref );

    }

    return $items;

  }


  // What a name looks like when a test uses it as what its section says it is - one pattern
  // per xref family, so the tag tidy, the option tidy and the @tidy@ construct stop
  // answering for each other. A family without a shape of its own falls back to the bare
  // word. Still regex over template source, so approximate - but approximate narrowly.

  function getReferencePattern ( $item, $xref ) {

    $q = preg_quote ( $item, '/' );

    if ( str_starts_with ( $xref, 'tag/pad'       ) ) return "/\{\/?$q\b/";
    if ( str_starts_with ( $xref, 'tag/common'    ) ) return "/\{\/?$q\b/";
    if ( str_starts_with ( $xref, 'config'        ) ) return "/'$q'/";
    if ( str_starts_with ( $xref, 'tag'           ) ) return "/\b$q:/";
    if ( str_starts_with ( $xref, 'properties'    ) ) return "/\b$q@|\{\/?$q\b|:$q\b/";
    if ( str_starts_with ( $xref, 'options/general' ) ) return "/\b$q\s*[=,}]/";
    if ( str_starts_with ( $xref, 'options/select'  ) ) return "/\b$q\s*[=,}]/";
    if ( str_starts_with ( $xref, 'functions'     ) ) return "/\|\s*$q\b|\b$q:/";
    if ( str_starts_with ( $xref, 'constructs'    ) ) return "/@$q@/";
    if ( str_starts_with ( $xref, 'at/properties' ) ) return "/\b$q@|\b$q\.\w+@/";
    if ( str_starts_with ( $xref, 'at'            ) ) return "/@$q\b|:$q\b/";

    return "/\b$q\b/";

  }


  // The text of a stored sandbox case, restored to what the template said: the colouring
  // wraps names in font tags, and PAD's own syntax characters travel as &open;-style
  // entities until the request ends - padUnescape() is the pair of that - so both are
  // undone before a pattern can meet the source spelling.

  function getReferenceText ( $code ) {

    return padUnescape ( html_entity_decode ( strip_tags ( $code ) ) );

  }


  // Every test whose source uses the item. Two kinds are read: the sandbox cases, from the
  // runs the suites keep in DATA/suites/ - a row with a 'code' field is a sandbox case,
  // restored to template text by getReferenceText() - and the pages tests, which are files
  // of the regression2 and regression3 applications, matched on their sources on disk. The
  // second kind is what covers the tags no sandbox case can hold: {error}, {redirect},
  // {exit} and the rest that end the request they stand in. The match is the section's
  // pattern from getReferencePattern(), so an option and a tag of the same name no longer
  // answer for each other.

  function getReferenceCaseList ( $item, $xref = '' ) {

    $match = getReferencePattern ( $item, $xref );

    $cases = [];

    foreach ( glob ( DATA . 'suites/*.json' ) as $file ) {

      $result = json_decode ( padFileGet ( $file ), TRUE );

      foreach ( $result ['tests'] ?? [] as $test ) {

        if ( ! isset ( $test ['code'] ) )
          continue;

        if ( ! preg_match ( $match, getReferenceText ( $test ['code'] ) ) )
          continue;

        $cases [] = [
          'suite'  => basename ( $file, '.json' ),
          'group'  => $test ['group'],
          'name'   => $test ['name'],
          'code'   => $test ['code'],
          'want'   => $test ['want'],
          'status' => $test ['status'],
          'link'   => 'regression/?sandbox/suites/' . basename ( $file, '.json' )
        ];

      }

    }

    foreach ( getReferencePagesTests () as $test )

      if ( preg_match ( $match, $test ['source'] ) )

        $cases [] = [
          'suite'  => $test ['suite'],
          'group'  => $test ['app'],
          'name'   => $test ['name'],
          'code'   => padColorsString ( $test ['source'] ),
          'want'   => htmlspecialchars ( $test ['want'] ),
          'status' => $test ['status'],
          'link'   => $test ['app'] . '/?' . $test ['name'] . '&padInclude'
        ];

    return $cases;

  }


  // The pages tests, one row per test: the two halves' sources joined, the recorded answer,
  // and the status of the last run. The two applications are named here rather than asked
  // for, so the reference application needs nothing from the regression one.

  function getReferencePagesTests () {

    static $tests = NULL;

    if ( $tests !== NULL )
      return $tests;

    $tests = [];

    foreach ( [ 'pages' => 'regression2', 'common' => 'regression3' ] as $suite => $app ) {

      $status = [];

      foreach ( json_decode ( padFileGet ( DATA . "suites/$suite.json" ), TRUE ) ['tests'] ?? [] as $row )
        $status [ $row ['name'] ] = $row ['status'];

      $directory = new RecursiveDirectoryIterator ( APPS . $app );
      $iterator  = new RecursiveIteratorIterator  ( $directory );

      foreach ( $iterator as $one ) {

        $path = padCorrectPath ( $one->getPathname () );

        if ( strpos ( $path, '/_' ) )
          continue;

        if ( ! str_ends_with ( $path, '.pad' ) )
          continue;

        $name = substr ( $path, strlen ( APPS . $app ) + 1, -4 );

        $source = padFileGet ( $path ) . padFileGet ( substr ( $path, 0, -4 ) . '.php' );

        // The root index is the frame test, fetched full - what it asserts includes the
        // application's wrapper, so the wrapper halves belong to its source. @page@ lives
        // there and nowhere a test file could say it.

        if ( $name == 'index' )
          $source .= padFileGet ( APPS . "$app/_inits.pad" ) . padFileGet ( APPS . "$app/_exits.pad" );

        $tests [] = [
          'suite'  => $suite,
          'app'    => $app,
          'name'   => $name,
          'source' => $source,
          'want'   => padFileGet ( substr ( $path, 0, -4 ) . '.txt' ),
          'status' => $status [$name] ?? ''
        ];

      }

    }

    return $tests;

  }


  // Whether any test uses the item at all - what the reference index needs, once per item,
  // so the sources are read and flattened only on the first ask.

  function getReferenceCased ( $item, $xref = '' ) {

    static $codes = NULL;

    if ( $codes === NULL ) {

      $codes = '';

      foreach ( glob ( DATA . 'suites/*.json' ) as $file ) {

        $result = json_decode ( padFileGet ( $file ), TRUE );

        foreach ( $result ['tests'] ?? [] as $test )
          if ( isset ( $test ['code'] ) )
            $codes .= "\n" . getReferenceText ( $test ['code'] );

      }

      foreach ( getReferencePagesTests () as $test )
        $codes .= "\n" . $test ['source'];

    }

    return (bool) preg_match ( getReferencePattern ( $item, $xref ), $codes );

  }

?>