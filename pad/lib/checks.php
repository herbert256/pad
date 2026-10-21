<?php

  // Two unrelated jobs: "does this application resource exist" lookups, and deciding
  // which globals belong to the application rather than to the engine.
  //
  // The lookups implement directory inheritance. padAppCheck walks the list from padDirs
  // (current page directory up to the application root) and returns the first match as a
  // path relative to APP2, so a subdirectory can override a parent. On top of it sit
  // padAppPageCheck, padAppIncludeCheck, padAppTagCheck, padAppFunctionCheck, and the
  // same idea for padScriptCheck, padCallBackCheck, padOptionCheck and padOptionEndCheck. padCommonCheck and
  // its two helpers look in the _common application instead; padCheck is the primitive
  // that accepts either a .pad or a .php file, padTagLib whether a built-in tag has code
  // of its own to load.
  //
  // padContentType sniffs a data block and names its format - list, json, yaml, xml, pad,
  // html, range, curl, file, else csv - and may trim $content to the part it recognised.
  //
  // padValidStore and padStrPad split the globals: the first says a name is application
  // data (not pad*/pq*, not a superglobal) and so may be handed to callbacks and code
  // blocks, the second says a name is engine state that a sandbox must reset, sparing the
  // padStr* machinery, the padStrSto stores, padLevelVars, the info counters and what the
  // debug toolbar collects across the passes of the request.
  // padValidFirstChar is the plain "starts with a letter" test.

  function padCommonCheck  ( $check ) { 
    
    if     ( padCommonTagCheck      ( $check ) ) return TRUE;
    elseif ( padCommonIncludeCheck  ( $check ) ) return TRUE;
    else                                         return FALSE;

  }
  
  function padCommonTagCheck      ( $check ) { return padCheck    ( COMMON . "_tags/$check"    ); }
  function padCommonIncludeCheck  ( $check ) { return padCheck    ( COMMON . "_include/$check" ); }

  function padAppPageCheck        ( $check ) { return padAppCheck ( $check                     ); }
  function padAppIncludeCheck     ( $check ) { return padAppCheck ( "_include/$check"          ); }
  function padAppTagCheck         ( $check ) { return padAppCheck ( "_tags/$check"             ); }
  function padAppFunctionCheck    ( $check ) { return padAppCheck ( "_functions/$check"        ); }

  // The answer is kept for the page's directory, and dropped when the second turns as the
  // padFileExists answers it is made of are: a page asked it some 1,700 times, a .pad and a
  // .php for every directory up to the root each time.

  function padAppCheck ( $check ) {

    static $memo = [], $at = 0;

    if ( ( $now = time () ) != $at ) {
      $memo = [];
      $at   = $now;
    }

    $key = ( $GLOBALS ['padDir'] ?? '' ) . "\0$check";

    if ( isset ( $memo [$key] ) )
      return $memo [$key];

    // Every caller hands it a name - a tag, a snippet, a page, a function - never a path: a
    // .. segment, a backslash or a NUL climbed out of the application, and content=$v with
    // ../../x found and ran any .php the walk reached.

    if ( str_contains ( $check, "\0" ) or str_contains ( $check, '\\' )
         or ( str_contains ( $check, '..' ) and in_array ( '..', explode ( '/', $check ), TRUE ) ) )
      return $memo [$key] = FALSE;

    foreach ( padDirs () as $value )
      if ( padCheck ( APP2 . "$value$check" ) )
        return $memo [$key] = $value . $check ;

    return $memo [$key] = FALSE;

  }

  // Kept as padFileExists keeps what it is made of, and dropped with it when the second
  // turns: every tag's name is checked in _tags and _include of the applications and in the
  // engine's tags, two lookups and two names put together each time.

  function padCheck ( $check ) {

    static $memo = [], $at = 0;

    if ( ( $now = time () ) != $at ) {
      $memo = [];
      $at   = $now;
    }

    return $memo [$check] ??= ( padFileExists ( "$check.pad" ) or padFileExists ( "$check.php" ) );

  }

  // Whether a file is there, the answer kept: a tag's name is looked for as a .pad and a .php
  // in _tags, _include and _options of every directory up to the application root, and the
  // answer is most often no - which PHP keeps nowhere, so each look was a call to the file
  // system, some twenty for every tag a page uses. The answers are dropped when the second
  // turns, so a worker or a stream that runs for minutes sees a file added or removed.

  function padFileExists ( $file ) {

    static $memo = [], $at = 0;

    if ( ( $now = time () ) != $at ) {
      $memo = [];
      $at   = $now;
    }

    return $memo [$file] ??= file_exists ( $file );

  }

  // The file of a built-in tag's own code, tags/_lib/<tag>.php - the functions only that tag
  // calls, which pad/lib/ would load on every request - or FALSE when it has none.
  // types/pad.php asks for every built-in tag it runs and includes the file the first time.
  // The answer is the file under its name on disk, whatever the case the tag was written in:
  // on a file system that ignores case {Tabs} is the tabs tag, and include_once of
  // tags/_lib/Tabs.php after tab.php's tags/_lib/tabs.php - or {avatar} and {Avatar} - ended
  // the page on "Cannot redeclare function".

  function padTagLib ( $tag ) {

    static $files = NULL;

    // Read with scandir, a fifth of what glob took for the same names - once per request,
    // and every request runs a built-in tag - and that listing kept between requests
    // (lib/dirs.php).

    if ( $files === NULL ) {
      $files = [];
      foreach ( padDirList ( PAD . 'tags/_lib' ) ?: [] as $file )
        if ( str_ends_with ( $file, '.php' ) )
          $files [ strtolower ( substr ( $file, 0, -4 ) ) ] = PAD . "tags/_lib/$file";
    }

    return $files [ strtolower ( $tag ) ] ?? FALSE;

  }

  // A script is named whole, with or without its extension - backup for backup.sh. The
  // name was a prefix of a glob, so any name a script's started with was that script: an
  // unknown {b} ran _scripts/backup.sh instead of being no tag at all. The answer is the
  // file itself, which the callers' glob hands back as it is.
  //
  // Kept like padAppCheck, for the page's directory and until the second turns: the
  // expression evaluator asks it for every bare word it classifies, a glob in each
  // directory up to the root each time.

  function padScriptCheck ( $check ) {

    static $memo = [], $at = 0;

    if ( ! padValidName ( $check ) )
      return FALSE;

    if ( ( $now = time () ) != $at ) {
      $memo = [];
      $at   = $now;
    }

    $key = ( $GLOBALS ['padDir'] ?? '' ) . "\0$check";

    if ( isset ( $memo [$key] ) )
      return $memo [$key];

    foreach ( padDirs () as $value ) {

      $script = APP2 . $value . "_scripts/$check";

      if ( is_file ( $script ) )
        return $memo [$key] = $script;

      // The directory is listed, not globbed: a bracketed route directory - blog/[year]/ -
      // read as a character class, and its scripts were never found.

      foreach ( is_dir ( dirname ( $script ) ) ? scandir ( dirname ( $script ) ) : [] as $file )
        if ( str_starts_with ( $file, "$check." ) and is_file ( dirname ( $script ) . "/$file" ) )
          return $memo [$key] = dirname ( $script ) . "/$file";

    }

    return $memo [$key] = FALSE;

  }

  function padCallBackCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    if ( ! str_ends_with ( $check, '.php' ) )
      $check .= '.php';

    foreach ( padDirs () as $value )
      if ( file_exists ( APP2 . $value . "_callbacks/$check" ) )
        return APP2 . $value . "_callbacks/$check";

    return FALSE;

  }

  function padOptionCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    foreach ( padDirs () as $value )
      if ( padFileExists ( APP2 . $value . "_options/$check.php" ) )
        return APP2 . $value . "_options/$check.php";

    return FALSE;

  }

  // The end phase of an application option: _options/end/name.php runs on the rendered
  // result of the tag, where _options/name.php runs on its template before anything is
  // rendered. An option may have either, or both.

  function padOptionEndCheck ( $check ) {

    if ( ! padValidName ( $check ) )
      return FALSE;

    foreach ( padDirs () as $value )
      if ( padFileExists ( APP2 . $value . "_options/end/$check.php" ) )
        return APP2 . $value . "_options/end/$check.php";

    return FALSE;

  }

  function padContentType ( &$content ) {

    $content = trim ( $content );

    if ( substr($content, 0, 1) == '(' and substr($content, -1) == ')' )
      $type = 'list';
    elseif ( substr ($content, 0, 6) == '&open;')
      $type = 'json';
    elseif ( substr ($content, 0, 5) == '%YAML' )
      $type = 'yaml';
    elseif ( substr ($content, 0, 3) == '---' )
      $type = 'yaml';
    elseif ( substr ( $content, 0, 5) == '<?xml')
      $type = 'xml';

    // A document with a DOCTYPE is XML, whatever the DOCTYPE says. One holding the letters
    // 'pad' anywhere - notepad.dtd, iPad, padding - was given the type 'pad', which has no
    // reader: "there is no data type named 'pad'", or a failed include with the strict check
    // off.

    elseif ( substr ( $content, 0, 1 ) == '<' and strpos ( $content, '<!DOCTYPE') !== FALSE )
      $type = 'xml';
    elseif ( substr ($content, 0, 5) == '<html' )
      $type = 'html';
    elseif ( substr($content, 0, 1) == '<')
      $type = 'xml';
    elseif ( substr($content, 0, 1) == '{')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '[')
      $type = 'json';
    elseif ( substr($content, 0, 1) == '(')
      $type = 'json';

    // A closing parenthesis at the very end said JSON: CSV whose last cell was Amsterdam (NL)
    // or an amount (100) ended the page "JSON conversion error". JSONP - cb({...}), cb([...])
    // - is told by its ({ and ([ below, its callback cut off.

    else
      $type = '';

    if ( $type )
      return $type;

    $first = strpos ($content, '({');
    $last  = strpos ($content, '})');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    $first = strpos ($content, '([');
    $last  = strpos ($content, '])');
    if ($first !== FALSE and $last !== FALSE and $first < $last ) {
      $type = 'json';
      $content = substr($content, $first+1, $last-$first);
      return $type;
    }

    // JSONP written with white space inside its parentheses - cb( [...] ), the bracket on
    // the next line, a ; after it - has no ({ or ([ side by side: once a closing ) at the
    // very end no longer said JSON, it read as CSV and iterated nothing. A text that ends
    // with the ) - and an optional ; - of a ( whose content is a bracketed document.

    if ( preg_match ( '/\(\s*([\[{].*[\]}])\s*\)\s*;?$/s', $content, $match ) ) {
      $content = $match [1];
      return 'json';
    }

    // Numeric ends of any sign or with decimals make a range too, as padGetRange counts them:
    // ctype_alnum refuses the - and the ., and -3..3, -5..-1 or 1.5..3 were read as a CSV
    // header with no rows - an empty set in silence, where {sequence '-3..3'} counts.

    $parts = padExplode ($content, '..');
    if ( count ($parts) == 2 and ( ( ctype_alnum($parts[0]) and ctype_alnum($parts[1]) )
                                   or ( is_numeric ($parts[0]) and is_numeric ($parts[1]) ) ) )
      return 'range';

    // SELF:// is this server, which data/curl.php turns into $padHost as {curl} does - but
    // only once the text has been taken for a URL, and the prefix was not one: data=
    // 'SELF://...' was read as CSV, a header line with no rows, and gave nothing.

    if ( str_starts_with ( strtolower ( $content ), 'http:' )
      or str_starts_with ( strtolower ( $content ), 'https:' )
      or str_starts_with ( $content, 'SELF://' ) )
      return 'curl';

    // YAML without its --- start: a block of lines opening - past its # comments - with a
    // list item ('- name: a') or a mapping key ('name: a', 'rows:') read as CSV, one column
    // named after the whole first line. A CSV header has its commas, a key none: the comma is
    // looked for in the key only, as the value of a key line may hold any text - 'city:
    // Paris, France' was read as CSV, one row whose field was named 'city: Paris'.

    if ( str_contains ( $content, "\n" ) ) {
      $line = trim ( preg_replace ( '/^(\s*#[^\n]*\n)+/', '', $content ) );
      $line = strtok ( $line, "\n" );
      if ( preg_match ( '/^-(\s|$)/', $line )
        or preg_match ( '/^[\w"\'][^:,]*:(\s|$)/', $line ) )
        return 'yaml';
    }

    if ( padDataFileName ( $content ) )
      return 'file';

    // A one-line name with a .. segment is a data name padDataFileName refused for walking
    // out of _data/, not a CSV header with no rows: data/file.php says so as an error,
    // where the walk was dropped without a word and the tag rendered nothing.

    if ( ! str_contains ( $content, "\n" ) and preg_match ( '#(^|[/\\\\])\.\.([/\\\\]|$)#', $content ) )
      return 'file';

    return 'csv';

  }

  // The answer per name is kept for the request: the same few hundred globals are asked
  // about again and again - every tag's PHP half snapshots the application's variables
  // before and after it runs (types/_go/tag.php), and the callbacks do the same per row.

  function padValidStore ($fld) {

    static $memo = [];

    return $memo [$fld] ??= ! padEngineName ( (string) $fld );

  }

  // The variables of $vars that are no engine names - what a tag compares before and after
  // its PHP half runs (types/_go/tag.php), and the globals group of the @ subsystem. Sifting
  // the whole symbol table name by name, even memoised, was most of the work of a page; the
  // engine's names, the large part of some 450 globals, now go in one array_diff_key of
  // PHP's own, and only what is left - the application's 10 to 40 and a name never seen
  // before - is looked at, each name classified once for the request.
  //
  // The answer holds the values, never references: inside a function-driven pass every
  // global is bound by reference (start/pad/function.php), and array_diff_key keeps a
  // reference as it is - a snapshot would follow the variable it was meant to remember.
  // The loop over what is left copies each value, as a loop over the whole table did.

  function padAppVars ( $vars ) {

    static $engine = [], $app = [];

    $out = [];

    foreach ( array_diff_key ( $vars, $engine ) as $name => $value )
      if ( isset ( $app [$name] ) )
        $out [$name] = $value;
      elseif ( padEngineName ( (string) $name ) )
        $engine [$name] = TRUE;
      else {
        $app [$name] = TRUE;
        $out [$name] = $value;
      }

    return $out;

  }

  // Kept per name for the request, like padValidStore: every nested pass sifts the whole
  // symbol table through it twice.
  //
  // What the response has issued stays issued whichever pass issued it - the CSRF token's
  // record, the CSP nonce, the flash messages taken out: rolled back at a sandbox's end, a
  // cached section kept one visitor's token for the next, the script nonce differed from the
  // header's, and a flash shown inside a {code} showed again outside it.

  function padStrPad ( $field ) {

    static $memo = [];

    if ( isset ( $memo [$field] ) )
      return $memo [$field];

    if ( str_starts_with ( $field, 'pad' ) or str_starts_with ( $field, 'pq' ) )
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, padStrSto) )
          if ( ! in_array ( $field, padLevelVars) )
            if ( ! in_array ( $field, [ 'padInfoCnt', 'padInfoTraceId', 'padToolbarData', 'padCsrfIssued', 'padNonce', 'padFlashNow' ], TRUE ) )
              return $memo [$field] = TRUE;

    return $memo [$field] = FALSE;

  }

  // The variables of $vars that padStrPad says a nested pass keeps, each name classified once
  // for the request, where a pass started and ended with a loop that asked padStrPad for
  // each of some 450 globals. The values are copied by the loop, never kept as references
  // (padAppVars says why).

  function padStrPadVars ( $vars ) {

    static $yes = [], $no = [];

    foreach ( array_diff_key ( $vars, $yes, $no ) as $name => $unused )
      if ( padStrPad ( (string) $name ) )
        $yes [$name] = TRUE;
      else
        $no [$name] = TRUE;

    $out = [];

    foreach ( array_intersect_key ( $vars, $yes ) as $name => $value )
      $out [$name] = $value;

    return $out;

  }

  function padValidFirstChar ($char) {

    if ( ctype_alpha ( $char) ) return TRUE;
    else                        return FALSE;

  }

?>
