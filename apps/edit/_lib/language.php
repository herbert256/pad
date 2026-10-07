<?php

  // What the editor knows about PAD and PHP: the names it completes, colours and explains.
  //
  // editLanguageBase  the same for every application, cached until a source changes:
  //   pad   the built-in names - editors/lsp/completions.json, which editors/generate.php
  //         writes from pad/ and ./ci.sh keeps in step with it - plus the sequence actions
  //         and the constructs, each with its documentation from docs/reference/*.md
  //   php   the PAD functions a page's PHP calls - the rows of docs/reference/HELPERS.md
  //         and of the Library Functions Reference in CLAUDE.md - with their real
  //         signatures; every internal PHP function with its signature; the $pad settings
  //         of pad/config/config.php with their defaults and comments; db()'s verbs
  // editLanguageApp   one application's own names, from its _xxx directories, each with the
  //                   directory it stands in, so the browser offers what PAD would find from
  //                   the file being edited (the directory up to the root, and _common)
  // editFields        where the fields of a template get their values: the page's paired
  //                   .php, the _inits.php and _exits.php of its directories, the _lib files
  //
  // The documentation lookup is editors/reference.js's (parseDoc, lookup) and the one-line
  // summary editors/mcp/pad-mcp.js's, in PHP.

  const editDocKinds = [ 'tag'       => [ 'TAGS.md', 'CONSTRUCTS.md' ],
                         'function'  => [ 'FUNCTIONS.md' ],
                         'option'    => [ 'OPTIONS.md', 'HANDLING.md' ],
                         'property'  => [ 'PROPERTIES.md' ],
                         'prefix'    => [ 'TYPES.md', 'TAGS.md' ],
                         'construct' => [ 'CONSTRUCTS.md' ] ];

  const editSingleTags = [ 'set', 'get', 'echo', 'increment', 'decrement', 'redirect', 'restart',
                           'exit', 'break', 'continue', 'cease', 'dump', 'error', 'exception',
                           'open', 'close', 'null', 'true', 'false', 'flag', 'page', 'curl',
                           'exists', 'at', 'resume', 'switch', 'ajax', 'reactData', 'file',
                           'make', 'keep', 'remove', 'action', 'debug', 'attrs', 'classes',
                           'trans', 'nonce', 'csrf', 'stack', 'recurse', 'parent', 'extends',
                           'meta', 'parms', 'assert', 'flush', 'sparkline', 'chart', 'input',
                           'textarea', 'pager', 'sitemap', 'qr', 'barcode' ];

  const editBranchTags = [ 'else', 'elseif', 'when' ];

  const editDbVerbs = [ 'ARRAY', 'RECORD', 'FIELD', 'CHECK', 'SELECT', 'INSERT', 'UPDATE',
                        'DELETE', 'REPLACE', 'SET', 'TRUNCATE', 'LOAD' ];

  function editLanguageBase () {

    $home    = editHome ();
    $sources = array_merge ( [ "$home/editors/lsp/completions.json", "$home/CLAUDE.md", PAD . 'config/config.php' ],
                             glob ( "$home/docs/reference/*.md" ) ?: [], glob ( PAD . 'lib/*.php' ) ?: [] );

    $stamp = PHP_VERSION . '|' . implode ( ',', array_map ( fn ( $f ) => (int) @filemtime ( $f ), $sources ) );

    return padRemember ( 'edit-language-' . md5 ( $stamp ), 86400 * 30, fn () => [
      'version' => md5 ( $stamp ),
      'pad'     => editLanguagePad (),
      'php'     => editLanguagePhp ()
    ] );

  }

  // ---------------------------------------------------------------------------------------
  // PAD's own names
  // ---------------------------------------------------------------------------------------

  function editLanguagePad () {

    $groups = [ 'PAD tag' => 'tags', 'pipe function' => 'functions', 'property@tag' => 'properties',
                'option' => 'options', 'type prefix' => 'prefixes', 'operator' => 'operators',
                'sequence type' => 'sequences' ];

    $kinds  = [ 'tags' => 'tag', 'functions' => 'function', 'properties' => 'property',
                'options' => 'option', 'prefixes' => 'prefix', 'constructs' => 'construct' ];

    $pad = array_fill_keys ( array_values ( $groups ), [] );

    $list = json_decode ( (string) @file_get_contents ( editHome () . '/editors/lsp/completions.json' ), TRUE ) ?: [];

    foreach ( $list as $one )
      if ( isset ( $groups [ $one ['detail'] ?? '' ] ) )
        $pad [ $groups [ $one ['detail'] ] ] [] = rtrim ( $one ['label'], ':' );

    $pad ['actions']    = editNames ( PAD . 'sequence/actions/types' );
    $pad ['constructs'] = editNames ( PAD . 'constructs' );
    $pad ['single']     = editSingleTags;
    $pad ['branch']     = editBranchTags;

    $pad ['docs'] = [];

    foreach ( $kinds as $group => $kind )
      foreach ( $pad [$group] as $name ) {
        $doc = editDocLookup ( $kind, $name );
        if ( $doc )
          $pad ['docs'] [$kind] [$name] = [ 'summary' => editDocSummary ( $doc ), 'text' => $doc ['text'] ];
      }

    return $pad;

  }

  function editNames ( $dir, $dirs = FALSE ) {

    $names = [];

    foreach ( (array) @scandir ( $dir ) as $one ) {

      $one = (string) $one;

      if ( $one === '' or $one [0] == '.' or $one [0] == '_' )
        continue;

      if ( $dirs and is_dir ( "$dir/$one" ) )
        $names [] = $one;
      elseif ( ! $dirs and str_ends_with ( $one, '.php' ) )
        $names [] = substr ( $one, 0, -4 );

    }

    sort ( $names, SORT_STRING | SORT_FLAG_CASE );

    return $names;

  }

  // One reference file as two indexes: the ### sections by the names in their heading, the
  // table rows by the `name` in their first cell, each row with its table's header.

  function editDocParse ( $file ) {

    static $cache = [];

    if ( isset ( $cache [$file] ) )
      return $cache [$file];

    $lines    = explode ( "\n", (string) @file_get_contents ( $file ) );
    $sections = $rows = [];
    $fence    = FALSE;
    $section  = NULL;
    $chapter  = '';
    $header   = '';

    $close = function ( $at ) use ( &$section, &$sections ) {
      if ( $section !== NULL )
        foreach ( $section ['keys'] as $key )
          $sections [$key] [] = [ 'heading' => $section ['heading'], 'start' => $section ['start'],
                                  'end' => $at, 'chapter' => $section ['chapter'] ];
      $section = NULL;
    };

    foreach ( $lines as $i => $line ) {

      if ( preg_match ( '/^\s*```/', $line ) )
        $fence = ! $fence;

      $heading = ! $fence && preg_match ( '/^(#{1,3})\s+(.*)$/', $line, $m );

      if ( $heading or ( ! $fence and preg_match ( '/^---\s*$/', $line ) ) ) {

        $close ( $i );

        if ( $heading and strlen ( $m [1] ) == 2 )
          $chapter = trim ( $m [2] );

        if ( $heading and strlen ( $m [1] ) == 3 ) {
          $keys = [];
          foreach ( preg_split ( '/\s+\/\s+|,\s*/', $m [2] ) as $part ) {
            $key = trim ( str_replace ( '`', '', $part ) );
            if ( preg_match ( '/^@?[A-Za-z_][A-Za-z0-9_]*@?$/', $key ) )
              $keys [] = $key;
          }
          $section = [ 'heading' => trim ( $m [2] ), 'start' => $i + 1, 'chapter' => $chapter, 'keys' => $keys ];
        }

      }

      if ( ! $fence and str_starts_with ( $line, '|' ) ) {

        if ( ! str_starts_with ( $lines [ $i - 1 ] ?? '', '|' ) ) {
          $header = $line;
          continue;
        }

        if ( preg_match ( '/^\|[\s:|-]+\|?\s*$/', $line ) )
          continue;

        $first = explode ( '|', $line ) [1] ?? '';

        preg_match_all ( '/`([^`]+)`/', $first, $all );

        foreach ( $all [1] as $raw ) {
          $key = preg_replace ( '/[:@]$/', '', preg_replace ( '/^\{|\}$/', '', trim ( $raw ) ) );
          if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $key ) )
            $rows [$key] [] = [ 'header' => $header, 'row' => $line, 'chapter' => $chapter ];
        }

      }

    }

    $close ( count ( $lines ) );

    return $cache [$file] = [ 'lines' => $lines, 'sections' => $sections, 'rows' => $rows ];

  }

  function editDocLookup ( $kind, $name ) {

    $key = ( $kind == 'construct' ) ? '@' . trim ( $name, '@' ) . '@' : $name;

    foreach ( editDocKinds [$kind] ?? [] as $fileName ) {

      $file = editHome () . "/docs/reference/$fileName";

      if ( ! is_file ( $file ) )
        continue;

      $doc = editDocParse ( $file );
      $sec = ( $doc ['sections'] [$key] ?? $doc ['sections'] ["@$key@"] ?? [] ) [0] ?? NULL;

      if ( $sec ) {
        $body = explode ( "\n", trim ( implode ( "\n", array_slice ( $doc ['lines'], $sec ['start'], $sec ['end'] - $sec ['start'] ) ) ) );
        if ( count ( $body ) > 40 )
          $body = array_merge ( array_slice ( $body, 0, 40 ), [ '...' ] );
        return [ 'section' => TRUE, 'text' => '### ' . $sec ['heading'] . "\n\n" . implode ( "\n", $body )
                                              . "\n\n*docs/reference/$fileName*" ];
      }

      $row = ( $doc ['rows'] [$key] ?? [] ) [0] ?? NULL;

      if ( $row ) {
        $cols = max ( 1, count ( explode ( '|', $row ['header'] ) ) - 2 );
        return [ 'section' => FALSE, 'row' => $row ['row'],
                 'text' => $row ['header'] . "\n|" . str_repeat ( ' --- |', $cols ) . "\n" . $row ['row']
                         . "\n\n*docs/reference/$fileName" . ( $row ['chapter'] ? ' - ' . $row ['chapter'] : '' ) . '*' ];
      }

    }

    return NULL;

  }

  function editDocSummary ( $doc ) {

    if ( $doc ['section'] ) {
      foreach ( array_slice ( explode ( "\n", $doc ['text'] ), 1 ) as $line ) {
        $line = trim ( $line );
        if ( $line !== '' and ! preg_match ( '/^(```|\||\*|#)/', $line ) )
          return $line;
      }
      return '';
    }

    $cells = array_slice ( explode ( '|', $doc ['row'] ), 2, -1 );

    return implode ( ' - ', array_filter ( array_map ( 'trim', $cells ), fn ( $c ) => $c !== '' and $c !== '-' ) );

  }

  // ---------------------------------------------------------------------------------------
  // PHP
  // ---------------------------------------------------------------------------------------

  function editLanguagePhp () {

    $documented = [];

    $helpers = (string) @file_get_contents ( editHome () . '/docs/reference/HELPERS.md' );
    $claude  = (string) @file_get_contents ( editHome () . '/CLAUDE.md' );
    $library = '';

    if ( preg_match ( '/^## Library Functions Reference\n(.*?)(?=^## |\z)/ms', $claude, $m ) )
      $library = $m [1];

    $chapter = '';

    foreach ( explode ( "\n", $helpers . "\n" . $library ) as $line ) {

      if ( preg_match ( '/^#{2,3}\s+(.*)$/', $line, $m ) )
        $chapter = trim ( $m [1] );

      if ( preg_match ( '/^\|\s*`(\w+)\s*\(([^`]*)\)`\s*\|\s*(.*?)\s*\|\s*$/', $line, $m ) and ! isset ( $documented [$m [1]] ) )
        $documented [$m [1]] = [ 'name' => $m [1], 'doc' => $m [3], 'group' => $chapter, 'sig' => "$m[1]($m[2])" ];

    }

    $pad = [];

    foreach ( $documented as $name => $one ) {
      if ( function_exists ( $name ) )
        $one ['sig'] = editSignature ( new ReflectionFunction ( $name ) );
      $pad [] = $one;
    }

    $internal = [];

    foreach ( get_defined_functions () ['internal'] as $name )
      try {
        $internal [] = [ 'name' => $name, 'sig' => editSignature ( new ReflectionFunction ( $name ) ) ];
      } catch ( Throwable $e ) {
        $internal [] = [ 'name' => $name, 'sig' => "$name(...)" ];
      }

    usort ( $internal, fn ( $a, $b ) => strcmp ( $a ['name'], $b ['name'] ) );

    return [ 'pad' => $pad, 'internal' => $internal, 'config' => editConfigNames (), 'verbs' => editDbVerbs,
             'keywords' => [ 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class',
                             'clone', 'const', 'continue', 'declare', 'default', 'do', 'echo', 'else',
                             'elseif', 'empty', 'enum', 'extends', 'final', 'finally', 'fn', 'for',
                             'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
                             'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list',
                             'match', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public',
                             'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw',
                             'trait', 'try', 'unset', 'use', 'var', 'while', 'xor', 'yield', 'TRUE',
                             'FALSE', 'NULL' ] ];

  }

  function editSignature ( ReflectionFunctionAbstract $function ) {

    $parms = [];

    foreach ( $function->getParameters () as $parm ) {

      $text = '';

      if ( $parm->hasType () )
        $text .= $parm->getType () . ' ';

      if ( $parm->isPassedByReference () ) $text .= '&';
      if ( $parm->isVariadic () )          $text .= '...';

      $text .= '$' . $parm->getName ();

      if ( $parm->isOptional () and ! $parm->isVariadic () ) {
        try {
          if ( $parm->isDefaultValueAvailable () )
            $text .= ' = ' . ( $parm->isDefaultValueConstant () ? $parm->getDefaultValueConstantName ()
                                                                : editExport ( $parm->getDefaultValue () ) );
          else
            $text .= ' = ?';
        } catch ( Throwable $e ) {
          $text .= ' = ?';
        }
      }

      $parms [] = $text;

    }

    $return = $function->hasReturnType () ? ': ' . $function->getReturnType () : '';

    return $function->getName () . '(' . implode ( ', ', $parms ) . ')' . $return;

  }

  function editExport ( $value ) {

    if ( $value === NULL )  return 'NULL';
    if ( $value === TRUE )  return 'TRUE';
    if ( $value === FALSE ) return 'FALSE';
    if ( is_array ( $value ) ) return $value ? '[...]' : '[]';
    if ( is_string ( $value ) ) return "'" . addcslashes ( $value, "'\\\n" ) . "'";
    if ( is_scalar ( $value ) ) return (string) $value;

    return '...';

  }

  // The $pad settings of the engine's configuration file, each with the default it sets and
  // the comment written above it - what completion offers in an application's config.php.

  function editConfigNames () {

    $lines   = explode ( "\n", (string) @file_get_contents ( PAD . 'config/config.php' ) );
    $comment = [];
    $names   = [];

    foreach ( $lines as $line ) {

      if ( preg_match ( '/^\s*\/\/ ?(.*)$/', $line, $m ) ) {
        $comment [] = $m [1];
        continue;
      }

      // A comment stands above its setting, a blank line in between or not.

      if ( trim ( $line ) === '' )
        continue;

      if ( preg_match ( '/^\s*\$(pad[A-Za-z0-9_]+)\s*=\s*(.*?);?\s*$/', $line, $m ) and ! isset ( $names [$m [1]] ) )
        $names [$m [1]] = [ 'name' => $m [1], 'value' => rtrim ( $m [2], ';' ), 'doc' => trim ( implode ( "\n", $comment ) ) ];

      $comment = [];

    }

    return array_values ( $names );

  }

  // ---------------------------------------------------------------------------------------
  // One application's own names
  // ---------------------------------------------------------------------------------------

  // Every directory of the application, '' for the root, with the names its _xxx directories
  // hold; and _common's when the application has _common switched on.

  function editLanguageApp ( $app ) {

    $base = editRoot ( $app, 'app' );
    $out  = [ 'common' => FALSE, 'names' => [], 'pages' => [], 'lib' => [] ];

    $dirs = [ '' ];

    foreach ( editTree ( $app ) as $one )
      if ( $one ['root'] == 'app' and $one ['dir'] and ! preg_match ( '#(^|/)_#', $one ['path'] ) )
        $dirs [] = $one ['path'];

    $where = [];

    foreach ( $dirs as $dir )
      $where [] = [ $dir, $base . ( $dir === '' ? '' : "$dir/" ) ];

    if ( $app != '_common' and editCommonOn ( $app ) ) {
      $out ['common'] = TRUE;
      $where [] = [ '_common', APPS . '_common/' ];
    }

    foreach ( $where as [ $dir, $path ] )
      editLanguageDir ( $dir, $path, $out );

    foreach ( editTree ( $app ) as $one )
      if ( $one ['root'] == 'app' and ! $one ['dir'] and ! preg_match ( '#(^|/)_#', $one ['path'] )
           and preg_match ( '/^(.*)\.(pad|html|php)$/', $one ['path'], $m ) )
        $out ['pages'] [$m [1]] = $m [1];

    $out ['pages'] = array_values ( $out ['pages'] );

    return $out;

  }

  // Whether an application runs with _common: its configuration does not switch it off.

  function editCommonOn ( $app ) {

    $config = (string) @file_get_contents ( APPS . "$app/_config/config.php" );

    return ! preg_match ( '/\$padCommon\s*=\s*(FALSE|false|0|NULL|null)\s*;/', $config );

  }

  function editLanguageDir ( $dir, $path, &$out ) {

    $add = function ( $kind, $name, $file, $doc = '' ) use ( &$out, $dir ) {
      $out ['names'] [] = [ 'kind' => $kind, 'name' => $name, 'dir' => $dir, 'file' => $file, 'doc' => $doc ];
    };

    $rel = fn ( $file ) => ( $dir === '' || $dir === '_common' ? '' : "$dir/" ) . $file;

    foreach ( [ '_tags' => [ 'tag', [ 'php', 'pad', 'html' ] ], '_functions' => [ 'function', [ 'php' ] ],
                '_include' => [ 'include', [ 'pad', 'html', 'php' ] ], '_options' => [ 'option', [ 'php' ] ],
                '_callbacks' => [ 'callback', [ 'php' ] ], '_mail' => [ 'mail', [ 'pad', 'html' ] ],
                '_scripts' => [ 'script', [ 'sh', 'php', 'pad' ] ],
                '_data' => [ 'data', [ 'json', 'xml', 'yaml', 'yml', 'csv', 'sql', 'curl', 'pad', 'php' ] ] ]
              as $sub => [ $kind, $exts ] )

      foreach ( (array) @scandir ( "$path$sub" ) as $file )
        if ( preg_match ( '/^([A-Za-z_][A-Za-z0-9_-]*)\.(' . implode ( '|', $exts ) . ')$/', (string) $file, $m ) )
          $add ( $kind, $m [1], $rel ( "$sub/$file" ), editFileSummary ( "$path$sub/$file" ) );

    foreach ( (array) @scandir ( "{$path}_content" ) as $file )
      if ( ! str_starts_with ( (string) $file, '.' ) and is_dir ( "{$path}_content/$file" ) )
        $add ( 'collection', $file, $rel ( "_content/$file" ) );

    foreach ( (array) @scandir ( "{$path}_lang" ) as $file )
      if ( str_ends_with ( (string) $file, '.json' ) )
        foreach ( array_keys ( editJsonRead ( "{$path}_lang/$file" ) ) as $key )
          $add ( 'lang', (string) $key, $rel ( "_lang/$file" ) );

    foreach ( (array) @scandir ( "{$path}_lib" ) as $file )
      if ( str_ends_with ( (string) $file, '.php' ) )
        editLibFunctions ( "{$path}_lib/$file", $rel ( "_lib/$file" ), $dir, $out );

  }

  // The functions a _lib file declares, each with its parameters and the comment above it.

  function editLibFunctions ( $file, $rel, $dir, &$out ) {

    $lines   = explode ( "\n", (string) @file_get_contents ( $file ) );
    $comment = [];

    foreach ( $lines as $i => $line ) {

      if ( preg_match ( '/^\s*\/\/ ?(.*)$/', $line, $m ) ) {
        $comment [] = $m [1];
        continue;
      }

      if ( preg_match ( '/^\s*function\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(([^)]*)\)/', $line, $m ) )
        $out ['lib'] [] = [ 'name' => $m [1], 'sig' => $m [1] . '(' . trim ( preg_replace ( '/\s+/', ' ', $m [2] ) ) . ')',
                            'doc' => trim ( implode ( "\n", $comment ) ), 'dir' => $dir, 'file' => $rel, 'line' => $i + 1 ];

      if ( trim ( $line ) !== '' )
        $comment = [];

    }

  }

  // The opening comment of a PHP file, or the first lines of a template.

  function editFileSummary ( $file ) {

    $text = (string) @file_get_contents ( $file, FALSE, NULL, 0, 4000 );

    if ( ! str_ends_with ( $file, '.php' ) )
      return '';

    $comment = [];

    foreach ( array_slice ( explode ( "\n", $text ), 1 ) as $line ) {
      if ( preg_match ( '/^\s*\/\/ ?(.*)$/', $line, $m ) )
        $comment [] = $m [1];
      elseif ( $comment or trim ( $line ) !== '' )
        break;
    }

    return trim ( implode ( "\n", $comment ) );

  }

  // ---------------------------------------------------------------------------------------
  // Where the fields of a template get their values
  // ---------------------------------------------------------------------------------------

  // For a template at $path (relative to the application root): every assignment of a
  // variable in its paired .php, the _inits.php and _exits.php of its directory chain and
  // the _lib files - name, file, line and the line's text. The context directory is the
  // template's own, cut above the first _xxx directory, as PAD searches from it.

  function editFields ( $app, $path ) {

    $base  = editRoot ( $app, 'app' );
    $parts = explode ( '/', (string) $path );
    $file  = array_pop ( $parts );
    $cut   = NULL;

    foreach ( $parts as $i => $part )
      if ( str_starts_with ( $part, '_' ) ) {
        $cut = $i;
        break;
      }

    $context = $cut === NULL ? $parts : array_slice ( $parts, 0, $cut );
    $files   = [];

    if ( $cut === NULL and ! str_starts_with ( $file, '_' ) and preg_match ( '/^(.*)\.(pad|html)$/', $file, $m ) )
      $files [] = implode ( '/', array_merge ( $parts, [ "$m[1].php" ] ) );

    for ( $n = count ( $context ); $n >= 0; $n-- ) {
      $dir = implode ( '/', array_slice ( $context, 0, $n ) );
      $pre = $dir === '' ? '' : "$dir/";
      $files [] = "{$pre}_inits.php";
      $files [] = "{$pre}_exits.php";
      foreach ( (array) @scandir ( "$base{$pre}_lib" ) as $lib )
        if ( str_ends_with ( (string) $lib, '.php' ) )
          $files [] = "{$pre}_lib/$lib";
    }

    $fields = [];

    foreach ( $files as $rel ) {

      if ( ! is_file ( $base . $rel ) )
        continue;

      foreach ( explode ( "\n", (string) file_get_contents ( $base . $rel ) ) as $i => $line ) {

        preg_match_all ( '/\$([A-Za-z_][A-Za-z0-9_]*)\s*(?:\[[^\]\n]*\]\s*)*(?:=(?![=>])|\.=|\+=|-=|\*=|\?\?=)|\bas\s+\$([A-Za-z_][A-Za-z0-9_]*)(?:\s*=>\s*\$([A-Za-z_][A-Za-z0-9_]*))?/',
                         $line, $all, PREG_SET_ORDER );

        foreach ( $all as $m )
          foreach ( [ $m [1] ?? '', $m [2] ?? '', $m [3] ?? '' ] as $name )
            if ( $name !== '' and $name != 'this' and ! str_starts_with ( $name, 'pad' ) and ! str_starts_with ( $name, 'GLOBALS' ) )
              $fields [] = [ 'name' => $name, 'file' => $rel, 'line' => $i + 1, 'text' => trim ( $line ) ];

      }

    }

    return $fields;

  }

?>
