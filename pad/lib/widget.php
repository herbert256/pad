<?php

  // What the widgets share - {tabs}, {accordion}, {modal}, {copy} and the rest of the widgets
  // subsystem in subs/widgets/ - with the other tags that draw and play, the charts, the games,
  // {video} and {chess} among them: an id of their own on the page, their colours as custom
  // properties, their CSS and script written once, the script with this request's CSP nonce.
  //
  // padWidgetTypes   the widgets: the files of subs/widgets/types/ (padKinds, lib/subsystem.php) -
  //                  a widget is added by adding its file there; the subsystem's own code is
  //                  in subs/widgets/, loaded when a page uses a widget
  // padWidgetType    whether a name is a widget
  // padWidgetId      a deterministic id: made from the widget's own content, so a widget
  //                  served from the fragment cache and one rendered next to it, or the
  //                  widgets of two nested passes, never share one - the same widget drawn
  //                  twice in a request gets a number behind the second (as padChartOpen):
  //                  -n2, which no item of the first - -1, -2 - can be
  // padWidgetOnce    TRUE the first time a key is asked for in its output - the CSS and
  //                  the script of a kind go out with the first widget of that kind only;
  //                  the tags that draw keep their style to one per output with it too
  // padWidgetColors  the custom properties of a class with their light-dark() defaults: a
  //                  page saying color-scheme: light dark gets the dark steps in dark mode,
  //                  a browser without light-dark() keeps the light ones
  // padWidgetStyle   a <style> with those properties and the rules, once per page - the rules
  //                  a file of www/assets/ (padWidgetFile) are a <link> behind it
  // padWidgetScript  a <script> once per page, carrying the nonce when $padCsp names one - a
  //                  file of www/assets/ is a <script src>
  // padWidgetSheet   a style sheet as its element: a <link> to a file of www/assets/, a
  //                  <style> of rules - the caller says once
  // padWidgetAttr    a text escaped for an attribute or an element's content
  // padWidgetAsset   the style or the script of a widget, a file of www/assets/widgets/
  //
  // The style sheets and the scripts the subsystems keep in files stand in www/assets/<sub>/ -
  // widgets/tabs.css, games/memory.js, charts/core.js - beside the entry points, served by the
  // web server as they are, so a browser keeps them between pages:
  //
  // padWidgetLinked   whether this output links those files - a <link rel="stylesheet"> and a
  //                  <script src>, the address with ?v= and a hash of the file's contents, so a
  //                  changed file is a new address - or has their text written in: linked on
  //                  a web page; written in when $padAssetLinks is off, in a mail or a
  //                  notification (a mail client fetches no style sheet), offline (pad pack:
  //                  no server behind the page), in a static copy (pad export) and in every
  //                  output that is not the web's
  // padWidgetFile    a file of www/assets/ - 'widgets/tabs.css' - as padWidgetStyle,
  //                  padWidgetScript and padWidgetSheet take it: the file to link, or its text
  //                  as the subsystem sends it written in; '' when it is not there or empty
  // padWidgetStat    the stat of a file of www/assets/, FALSE when it is no file there
  // padWidgetFileText  the text of a file of www/assets/, kept while its time and size stand
  // padWidgetUrl     the address of a file of www/assets/, with its version
  // padWidgetHash    that version, kept between requests with the file's inode, times and
  //                  size in DATA/cache/assets/ - as lib/dirs.php keeps the listings - so a
  //                  page that links a file costs one stat of it
  // padWidgetHead    the style sheets of www/assets/ a page links, moved into its <head>
  // padWidgetWhole   whether the output is a whole page, not a part of one
  //
  // What these write is protected (padProtect): their braces are CSS and JavaScript, never
  // tags of the page around them - a tag that hands its rendering back through $padContent
  // at the end walk has it spliced into the text its parent goes on to scan.

  function padWidgetTypes () {

    return padKinds ( 'widgets' );

  }

  function padWidgetType ( $name ) {

    return padKind ( 'widgets', $name );

  }

  // A number option of a tag that draws - size=, stroke= - or its default, named under the
  // strict check: '1.5em', 'small' and 'large' were drawn at the 8px minimum without a word,
  // where the same tags name every other option they cannot use. A number written with px,
  // the unit it is in - size='32px' - is that number: it was drawn at 32 before the check.

  function padWidgetNumber ( $tag, $option, $default ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = preg_replace ( '/\s*px\s*$/i', '', $value );

    if ( is_numeric ( $value ) and is_finite ( (float) $value ) )
      return $value + 0;

    if ( $padCheckSyntax )
      padError ( "the $tag has no usable $option '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 ) . "' - a number of pixels" );

    return $default;

  }

  function padWidgetId ( $kind, $seed ) {

    global $pad, $padFragment;

    static $drawn = [];

    // A widget made inside a {cache} section that is rendering - to be kept - is seeded with
    // the section's key as well: kept, it comes back on a later request beside a widget of
    // the same names made then, and the two had one id and one radio group - choosing a tab
    // in one emptied the other.

    for ( $i = $pad ?? -1; $i >= 0; $i-- )
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] ) {
        $seed = [ $seed, $padFragment [$i] ['key'] ];
        break;
      }

    $id = "pad-$kind-" . substr ( md5 ( serialize ( $seed ) ), 0, 8 );

    $drawn [$id] = ( $drawn [$id] ?? 0 ) + 1;

    return ( $drawn [$id] > 1 ) ? $id . '-n' . $drawn [$id] : $id;

  }

  // Once per output: the page, a {cache} section that is being kept - it comes back on
  // other pages, where nothing else wrote its CSS and script - a mail rendered during the
  // request ($padOnceScope, lib/mail.php) and, for a style, a {shadow} root ($padShadowScope,
  // tags/shadow.php). Remembered for the PHP process, a section
  // was kept without the style or the script an earlier widget of its page had written -
  // a video's play button did nothing elsewhere - and a mail took the page's style.

  function padWidgetOnce ( $key ) {

    global $pad, $padFragment, $padOnceScope, $padShadowScope;

    static $done = [];

    $scope = (string) ( $padOnceScope ?? '' );

    // A {shadow} root takes no style from the page: what a widget writes once - its style,
    // the rules of its slots - is written once in every shadow root as well; a script runs
    // for the whole document and stays once a page.

    if ( $padShadowScope and ! str_starts_with ( $key, 'script:' ) )
      $scope .= '|' . end ( $padShadowScope );

    for ( $i = $pad ?? -1; $i >= 0; $i-- )
      if ( is_array ( $padFragment [$i] ?? NULL ) and ! $padFragment [$i] ['hit'] ) {
        $scope .= '|' . $padFragment [$i] ['key'];
        break;
      }

    if ( isset ( $done [$scope] [$key] ) )
      return FALSE;

    $done [$scope] [$key] = TRUE;

    return TRUE;

  }

  function padWidgetColors ( $class, $roles ) {

    $light = $both = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-$class-$role:$day;";
      $both  .= "--pad-$class-$role:light-dark($day,$night);";
    }

    return ":where(.pad-$class){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-$class){{$both}}}";

  }

  // The colours stay a <style> of the page: they are made here, from the roles. The rules of
  // a file of www/assets/ follow them as a <link>, which padWidgetHead moves into the <head>
  // of the page with the other links.

  function padWidgetStyle ( $class, $roles, $rules ) {

    if ( ! padWidgetOnce ( "style:$class" ) )
      return '';

    if ( is_array ( $rules ) )
      return padProtect ( '<style' . padStyleNonce () . '>' . padWidgetColors ( $class, $roles ) . '</style>' )
           . padWidgetSheet ( $rules );

    return padProtect ( '<style' . padStyleNonce () . '>' . padWidgetColors ( $class, $roles ) . $rules . '</style>' );

  }

  // A script of www/assets/ is a <script src> without async or defer: it runs where it stands,
  // before what follows it, as the script written in did - a kind's script after the runtime
  // it extends, the inline line that mounts a chart after the code that draws it.

  function padWidgetScript ( $key, $script ) {

    global $padCsp;

    if ( ! padWidgetOnce ( "script:$key" ) )
      return '';

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    if ( is_array ( $script ) )
      return padProtect ( '<script src="' . htmlspecialchars ( $script ['url'], ENT_QUOTES ) . "\"$nonce></script>" );

    return padProtect ( "<script$nonce>$script</script>" );

  }

  // A style sheet: a file of www/assets/ as a <link>, rules as a <style>, nothing for ''. The
  // caller asks padWidgetOnce first.

  function padWidgetSheet ( $css ) {

    if ( is_array ( $css ) )
      return padProtect ( '<link rel="stylesheet" href="' . htmlspecialchars ( $css ['url'], ENT_QUOTES ) . '"'
                          . padStyleNonce () . '>' );

    if ( (string) $css === '' )
      return '';

    return padProtect ( '<style' . padStyleNonce () . ">$css</style>" );

  }

  // A style sheet or a script of a widget, a file of www/assets/widgets/. Written in, a style
  // sheet there is written a rule a line and sent as one line - the line breaks and the indent
  // after them taken out, which a rule of these never holds - as the rules were sent when they
  // stood in the PHP; a script is sent as it stands, without the line break that ends the file.

  function padWidgetAsset ( $file ) {

    return padWidgetFile ( "widgets/$file" );

  }

  // ------------------------------------------------------------------------------------
  // The files of www/assets/.
  // ------------------------------------------------------------------------------------

  // Linked, or written in. A {cache} section kept in a page links the files as the page
  // does; a mail rendered during the request has them written in, the page around it links.

  function padWidgetLinked () {

    global $padAssetLinks, $padOnceScope, $padOutputType, $padExportRun;

    return ( $padAssetLinks ?? TRUE )
       and ( $padOutputType ?? 'web' ) === 'web'
       and ! ( $padExportRun ?? FALSE )
       and ! preg_match ( '/^(mail|notify)/', (string) ( $padOnceScope ?? '' ) )
       and ! padWasm ();

  }

  // A file of www/assets/ as padWidgetStyle, padWidgetScript and padWidgetSheet take it.
  // Written in, its text is sent as the subsystem sends it - $how:
  //
  //   line   a style sheet as one line, the line breaks and the indent after them taken out;
  //          a script as it stands, without the line break that ends the file
  //   bare   a style sheet as one line without its comments
  //   raw    the file as it stands
  //
  // Linked or written in, a file that is not there or is empty is '', which a caller with an
  // optional file - the style of a kind that has none - tests for. Linked, the file is not
  // read: its stat says it is there and not empty, and the hash in its address is kept with
  // that stat between requests (padWidgetHash) - one stat a file a request.

  function padWidgetFile ( $path, $how = 'line' ) {

    if ( padWidgetLinked () ) {
      $stat = padWidgetStat ( $path );
      return ( $stat and $stat ['size'] > 0 ) ? [ 'padWidgetFile' => $path, 'url' => padWidgetUrl ( $path, $stat ) ] : '';
    }

    $text = padWidgetFileText ( $path );

    if ( $text === '' )
      return '';

    if ( $how == 'raw' )
      return $text;

    if ( ! str_ends_with ( $path, '.css' ) )
      return rtrim ( $text, "\r\n" );

    if ( $how == 'bare' )
      return trim ( preg_replace ( '/\R[ \t]*/', '', trim ( preg_replace ( '#/\*.*?\*/#s', '', $text ) ) ) );

    return preg_replace ( '/\R[ \t]*/', '', trim ( $text ) );

  }

  // The directory: www/assets/ of the PAD home the engine stands in.

  function padWidgetDir () {

    return dirname ( PAD ) . '/www/assets/';

  }

  // The stat of a file of www/assets/ - a name below it, never climbing out - FALSE when it is
  // no file there.

  function padWidgetStat ( $path ) {

    if ( ! preg_match ( '#^[a-z0-9_-]+/[a-z0-9_.-]+$#iD', $path ) or str_contains ( $path, '..' ) )
      return FALSE;

    $stat = @stat ( padWidgetDir () . $path );

    return ( $stat and ( $stat ['mode'] & 0170000 ) == 0100000 ) ? $stat : FALSE;

  }

  // The text of a file of www/assets/, read once while its time and size stand: a queue worker
  // renders mail after mail in one process.

  function padWidgetFileText ( $path ) {

    static $memo = [];

    $stat = padWidgetStat ( $path );

    if ( ! $stat )
      return '';

    $key = "$path|{$stat ['mtime']}|{$stat ['size']}";

    return $memo [$key] ??= (string) @file_get_contents ( padWidgetDir () . $path );

  }

  // The address of a file of www/assets/ - /<mount>/assets/widgets/tabs.css?v=3f9a1c02be -
  // with ten hex digits of a hash of its contents, as padAsset gives a file of an
  // application's own. $stat is the file's, when the caller has it.

  function padWidgetUrl ( $path, $stat = NULL ) {

    global $padRoot;

    $stat ??= padWidgetStat ( $path );
    $hash   = $stat ? padWidgetHash ( $path, $stat ) : '';

    return ( $padRoot ?? '/' ) . "assets/$path" . ( ( $hash !== '' ) ? "?v=$hash" : '' );

  }

  // The hash of a file of www/assets/, kept between requests as lib/dirs.php keeps the listing
  // of a directory: with the file's inode, its times and its size, and made again - the file
  // read and hashed - when one of them changed; the change time moves on too when a tool that
  // copies a tree (rsync, tar) sets the modification time back. A file changed within the last
  // two seconds is not kept: a time has whole seconds, and a second change in that second, of
  // the same size, would have left them all the same.
  //
  // The hashes stand in DATA/cache/assets/hashes.json, read once for the request and written
  // again, once at its end, only when one in it changed: written at each change, the first
  // page after a deploy that changed every file wrote it once a file, 75 ms for all 226.
  // Without a writable DATA every request reads and hashes the files it links, as before.

  function padWidgetHash ( $path, $stat ) {

    static $write = FALSE;

    $store = & padWidgetHashes ();
    $now   = [ $stat ['ino'], $stat ['mtime'], $stat ['ctime'], $stat ['size'] ];
    $kept  = $store [$path] ?? NULL;

    if ( is_array ( $kept ) and count ( $kept ) == 5 and array_slice ( $kept, 0, 4 ) === $now and is_string ( $kept [4] ) )
      return $kept [4];

    $hash = substr ( hash ( in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256', padWidgetFileText ( $path ) ), 0, 10 );

    if ( time () - max ( $stat ['mtime'], $stat ['ctime'] ) >= 2 ) {
      $store [$path] = [ $now [0], $now [1], $now [2], $now [3], $hash ];
      if ( ! $write )
        register_shutdown_function ( 'padWidgetHashesKeep' );
      $write = TRUE;
    }

    return $hash;

  }

  function & padWidgetHashes () {

    static $store = NULL;

    if ( $store === NULL ) {
      $store = json_decode ( (string) @file_get_contents ( DATA . 'cache/assets/hashes.json' ), TRUE );
      if ( ! is_array ( $store ) )
        $store = [];
    }

    return $store;

  }

  // At the end of the request - of the process, for a queue worker - what is written is what
  // is read by every request: a file that is gone goes out of it.

  function padWidgetHashesKeep () {

    $store = & padWidgetHashes ();

    foreach ( $store as $path => $unused )
      if ( ! is_file ( padWidgetDir () . $path ) )
        unset ( $store [$path] );

    $file = DATA . 'cache/assets/hashes.json';
    $json = json_encode ( $store, JSON_UNESCAPED_SLASHES );

    if ( $json === FALSE or ! ( is_dir ( DATA . 'cache/assets' ) or @mkdir ( DATA . 'cache/assets', 0777, TRUE ) ) )
      return;

    $tmp = $file . '.' . getmypid () . '.' . bin2hex ( random_bytes ( 4 ) );

    if ( @file_put_contents ( $tmp, $json ) === strlen ( $json ) and @rename ( $tmp, $file ) )
      return;

    @unlink ( $tmp );

  }

  // The style sheets of www/assets/ a page links stand where the tags wrote them - in a
  // fragment, a live region's answer, a kept {cache} section they must. In a whole page they
  // are moved into its <head>, each once, as tidy moves a <style>: a <link> in a paragraph
  // ended it under tidy, and one in the inline <svg> an effect keeps its style in was never
  // loaded. A whole page without a <head> of its own - a bare template, which tidy makes a
  // document of - has them at its start, where tidy puts them into the head it makes and a
  // browser into the one it implies. A link inside a <template shadowrootmode> - a {shadow}
  // root, whose style the page around it does not reach - stays.

  function padWidgetHead ( $html ) {

    global $padRoot;

    $prefix = '<link rel="stylesheet" href="' . htmlspecialchars ( ( $padRoot ?? '/' ) . 'assets/', ENT_QUOTES );

    if ( ! str_contains ( $html, $prefix ) )
      return $html;

    $at = stripos ( $html, '</head>' );

    if ( $at === FALSE ) {
      if ( ! padWidgetWhole () )
        return $html;
      $at = preg_match ( '#^\s*(?:<!DOCTYPE[^>]*>\s*)?(?:<html\b[^>]*>)?#i', $html, $m ) ? strlen ( $m [0] ) : 0;
    }

    $links = [];

    $body = preg_replace_callback (
      '#<template\b[^>]*\bshadowrootmode\b.*?</template>|' . preg_quote ( $prefix, '#' ) . '[^"]*"(?: nonce="[^"]*")?>#is',
      function ( $m ) use ( &$links ) {
        if ( strncasecmp ( $m [0], '<template', 9 ) == 0 )
          return $m [0];
        $links [$m [0]] = TRUE;
        return '';
      },
      substr ( $html, $at ) );

    if ( $body === NULL )
      return $html;

    $head = substr ( $html, 0, $at );

    foreach ( array_keys ( $links ) as $link )
      if ( ! str_contains ( $head, $link ) )
        $head .= $link;

    return $head . $body;

  }

  // Whether the output is a whole page - what tidy reads as one (exits/tidy.php): not a
  // response fragment, a live region's answer, a page included without its wrappers, the
  // rest of a page that flushed its head, nor an answer that is no HTML.

  function padWidgetWhole () {

    global $padFragmentSent, $padContentType;

    if ( isset ( $_REQUEST ['padInclude'] ) or ( $padFragmentSent ?? FALSE ) or padLive () !== '' or isset ( $GLOBALS ['padFlushRaw'] ) )
      return FALSE;

    if ( function_exists ( 'padSelfSwitch' ) and ( padSelfSwitch ( 'padExamples' ) or padSelfSwitch ( 'padReference' ) ) )
      return FALSE;

    return str_starts_with ( strtolower ( trim ( $padContentType ?? 'text/html' ) ), 'text/html' );

  }

  function padWidgetAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
