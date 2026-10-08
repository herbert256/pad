<?php

  // The applications of this installation, as the console sees them.
  //
  // An application is a directory of apps/ with an entry point of its own in www/ - what
  // makes a name fetchable. A directory without one that holds applications, like
  // regression/, is a namespace: its applications keep it in their name. _common is listed
  // as well, as the shared resources it is, without a link.
  //
  // adminApps          name => [ name, dir, www, link, group, description ], sorted
  // adminAppKnown      whether a name is one of them
  // adminAppAsked      the application the request names in app=, or ''
  // adminAppInfo       what the application page and the list show of one: its pages,
  //                    files, size, the PAD directories it has, its state under DATA/
  // adminAppPages      the pages of an application, as the router reaches them
  // adminReadmeIntro   the Introduction section of a README.md, as one line

  function adminHome () {

    return dirname ( APPS ) . '/';

  }

  function adminApps () {

    static $apps = NULL;

    if ( $apps !== NULL )
      return $apps;

    $apps = [];

    adminAppsWalk ( '', $apps, 0 );

    if ( is_dir ( APPS . '_common' ) )
      $apps ['_common'] = adminAppRow ( '_common', '' );

    uksort ( $apps, function ( $a, $b ) {
      if ( $a === '_common' ) return -1;
      if ( $b === '_common' ) return 1;
      return strcasecmp ( $a, $b );
    } );

    return $apps;

  }

  function adminAppsWalk ( $prefix, &$apps, $depth ) {

    if ( $depth > 3 )
      return;

    foreach ( scandir ( APPS . $prefix ) ?: [] as $dir ) {

      if ( $dir [0] === '.' or $dir [0] === '_' or ! is_dir ( APPS . "$prefix$dir" ) )
        continue;

      if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]*$/D', $dir ) )
        continue;

      $name = "$prefix$dir";

      if ( is_file ( adminHome () . "www/$name/index.php" ) )
        $apps [$name] = adminAppRow ( $name, $prefix === '' ? '' : rtrim ( $prefix, '/' ) );
      else
        adminAppsWalk ( "$name/", $apps, $depth + 1 );

    }

  }

  function adminAppRow ( $name, $group ) {

    global $padRoot;

    return [
      'name'        => $name,
      'dir'         => APPS . "$name/",
      'www'         => is_dir ( adminHome () . "www/$name" ) ? adminHome () . "www/$name/" : '',
      'link'        => $name === '_common' ? '' : $padRoot . "$name/",
      'group'       => $group,
      'description' => adminReadmeIntro ( APPS . "$name/README.md" )
    ];

  }

  function adminAppKnown ( $app ) {

    return is_string ( $app ) and isset ( adminApps () [$app] );

  }

  function adminAppAsked ( $key = 'app' ) {

    $app = padRequest ( $key, '' );

    return adminAppKnown ( $app ) ? $app : '';

  }

  function adminReadmeIntro ( $file ) {

    if ( ! is_file ( $file ) )
      return '';

    if ( ! preg_match ( '/## Introduction\s*\n+(.+?)(?=\n## |\n#|\z)/s', (string) file_get_contents ( $file ), $match ) )
      return '';

    return trim ( preg_replace ( '/\s+/', ' ', $match [1] ) );

  }

  // The PAD directories and files an application can have, and what each is for - the
  // application page shows the ones there are, the list a badge for some of them.

  function adminAppParts () {

    return [
      '_config'     => 'configuration overrides',
      '_lib'        => 'PHP functions, included on every request',
      '_include'    => 'template snippets',
      '_tags'       => 'custom tags',
      '_functions'  => 'pipe functions',
      '_callbacks'  => 'iteration callbacks',
      '_options'    => 'tag options',
      '_events'     => 'event hooks',
      '_data'       => 'data files and named queries',
      '_lang'       => 'translation catalogs',
      '_content'    => 'Markdown collections',
      '_errors'     => 'error pages',
      '_mail'       => 'mail templates',
      '_migrations' => 'schema migrations',
      '_seeds'      => 'seeders',
      '_jobs'       => 'queue job handlers',
      '_tests'      => 'application tests',
      '_samples'    => 'designer sample data',
      '_scripts'    => 'shell scripts',
      '_guard.php'  => 'access guard',
      '_inits.php'  => 'runs before every page',
      '_inits.pad'  => 'wrapper around every page',
      '_exits.php'  => 'runs after every page',
      '_exits.pad'  => 'closing wrapper',
      '_schedule.php' => 'scheduled entries',
      '_health.php' => 'health checks of ?up',
      '.env'        => 'environment values (padEnv)'
    ];

  }

  function adminAppInfo ( $app ) {

    $row   = adminApps () [$app];
    $dir   = $row ['dir'];
    $parts = [];

    foreach ( adminAppParts () as $part => $what )
      if ( file_exists ( $dir . $part ) )
        $parts [] = [ 'part' => $part, 'what' => $what,
                      'count' => is_dir ( $dir . $part ) ? count ( adminFilesIn ( $dir . $part ) ) : 1 ];

    $files = $bytes = $lines = $newest = 0;
    $newestFile = '';
    $kinds = [];

    foreach ( adminFilesIn ( $dir, TRUE ) as $file ) {

      $files++;
      $size   = (int) @filesize ( $file );
      $bytes += $size;
      $ext    = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );

      $kinds [$ext] = ( $kinds [$ext] ?? 0 ) + 1;

      if ( in_array ( $ext, [ 'pad', 'php', 'html', 'js', 'css' ] ) and $size < 1048576 )
        $lines += substr_count ( (string) file_get_contents ( $file ), "\n" );

      $time = (int) @filemtime ( $file );

      if ( $time > $newest ) {
        $newest     = $time;
        $newestFile = substr ( $file, strlen ( $dir ) );
      }

    }

    arsort ( $kinds );

    $kindRows = [];

    foreach ( $kinds as $ext => $count )
      $kindRows [] = [ 'ext' => $ext === '' ? '(none)' : $ext, 'count' => $count ];

    return $row + [
      'parts'      => $parts,
      'files'      => $files,
      'bytes'      => $bytes,
      'size'       => adminBytes ( $bytes ),
      'lines'      => $lines,
      'kinds'      => $kindRows,
      'newest'     => $newest,
      'newestFile' => $newestFile,
      'down'       => padMaintenanceRead ( $app ) ? 1 : 0,
      'queued'     => adminQueueCount ( $app ),
      'failed'     => count ( glob ( DATA . "queue/$app/_failed/*.json" ) ?: [] ),
      'logs'       => count ( glob ( DATA . "logs/$app/*.log" ) ?: [] ),
      'dumps'      => count ( glob ( DATA . "dumps/$app/*", GLOB_ONLYDIR ) ?: [] ),
      'mails'      => count ( glob ( DATA . "mail/$app/*.eml" ) ?: [] ),
      'migrations' => count ( preg_grep ( '/\.down\.sql$/', glob ( $dir . '_migrations/*.{sql,php}', GLOB_BRACE ) ?: [] , PREG_GREP_INVERT ) ),
      'tests'      => count ( glob ( $dir . '_tests/*.txt' ) ?: [] )
    ];

  }

  // Every file under a directory, its own _xxx directories included when $deep, never what
  // starts with a dot nor what belongs to an application nested in it.

  function adminFilesIn ( $dir, $deep = TRUE ) {

    $dir  = rtrim ( $dir, '/' ) . '/';
    $list = [];

    foreach ( scandir ( $dir ) ?: [] as $item ) {

      if ( $item [0] === '.' )
        continue;

      if ( is_dir ( $dir . $item ) ) {

        if ( $deep and ! adminNestedApp ( $dir . $item ) )
          $list = array_merge ( $list, adminFilesIn ( $dir . $item, TRUE ) );

      } else
        $list [] = $dir . $item;

    }

    return $list;

  }

  function adminNestedApp ( $path ) {

    $name = substr ( rtrim ( $path, '/' ), strlen ( APPS ) );

    return isset ( adminApps () [$name] );

  }

  // The pages of an application: the names the router reaches - a .pad, .html or .php
  // outside the _xxx directories, a pair counted once - with whether a template and a PHP
  // file are there. A bracketed name is a route a path fills in.

  function adminAppPages ( $app, $prefix = '' ) {

    $dir   = APPS . "$app/$prefix";
    $pages = $dirs = [];

    foreach ( scandir ( $dir ) ?: [] as $item ) {

      if ( $item [0] === '.' or $item [0] === '_' )
        continue;

      if ( is_dir ( $dir . $item ) ) {
        if ( ! adminNestedApp ( $dir . $item ) )
          $dirs [] = $item;
        continue;
      }

      if ( ! preg_match ( '/^(.+)\.(pad|php|html)$/', $item, $match ) )
        continue;

      $name = $prefix . $match [1];

      $pages [$name] ??= [ 'page' => $name, 'pad' => 0, 'php' => 0, 'html' => 0,
                           'route' => str_contains ( $name, '[' ) ? 1 : 0 ];

      $pages [$name] [ $match [2] ] = 1;

    }

    foreach ( $dirs as $one )
      $pages += adminAppPages ( $app, "$prefix$one/" );

    ksort ( $pages );

    return $pages;

  }

  // The settings an application's _config/*.php files make: every $name = value; at the
  // start of a line, as written, a password's or key's value left out.

  function adminConfigOf ( $app ) {

    $rows = [];

    foreach ( glob ( APPS . "$app/_config/*.php" ) ?: [] as $file )
      if ( preg_match_all ( '/^\s*\$([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.+?);\s*$/m', (string) file_get_contents ( $file ), $matches, PREG_SET_ORDER ) )
        foreach ( $matches as $match )
          $rows [] = [ 'name' => $match [1], 'value' => adminRedactValue ( $match [1], $match [2] ),
                       'file' => basename ( $file ) ];

    return $rows;

  }

  function adminQueueCount ( $app ) {

    $count = 0;

    foreach ( glob ( DATA . "queue/$app/*", GLOB_ONLYDIR ) ?: [] as $queue )
      if ( basename ( $queue ) [0] !== '_' )
        $count += count ( glob ( "$queue/*.job" ) ?: [] );

    return $count;

  }

?>
