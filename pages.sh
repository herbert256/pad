#!/usr/bin/env bash

# Rebuilds docs/apps - the static copy of the PAD applications that GitHub Pages serves at
# herbert256.github.io/pad/apps/ - from the running site, where it was a SiteSucker crawl
# made by hand.
#
# The crawl starts at the PAD home and follows every link into the applications listed in
# the php below, the way a visitor clicks through them: each request carries a forwarded
# address, so the engine answers as it does a visitor - no debug toolbar, no live reload,
# no dumps. A page is saved under the name SiteSucker gave it, which is the URL the copy
# is published under: /pad/manual/?pages/forms is manual/index﹖pagesꤷforms.html (the
# question mark and the slash of the query in their look-alike characters, the query
# decoded first - %2F is a slash too), the PAD home is pad/index.html and, a second time,
# the copy's own index.html. A file is saved under its path alone - a ?v= behind a
# stylesheet or a picture only names its version. Every link to a saved page or file - an
# href, a src, a poster, each picture of a srcset - is rewritten to the relative path of its
# copy; a link elsewhere - another site, an application left out - stays as it is. The
# modules a script imports by a relative name - the chunks a Vite build loads when an
# island needs them - are fetched along and keep their names beside it.
#
# Left out are the pages that act when they are fetched - a build, the suites' test run,
# recording an answer, every page of develop but its index - the months a calendar links to,
# one after the other without end, and the applications a copy cannot serve: edit and admin
# (a login on this machine), playground, test and the suites' own test pages. The
# applications page links each application to the editor; in the copy that link is the
# read-only browse page, which a static site can show - and the menu's admin is the
# applications page.
#
# The copy is built in a directory of its own and replaces docs/apps once the PAD home was
# fetched. A page that answers an error - the manual links a few that fail on purpose, to
# show the error - is named and left out, its links pointing where they did.
#
#   ./pages.sh                          # crawls http://localhost/pad/
#   ./pages.sh http://127.0.0.1:8765/   # another server

. "$(dirname "$0")/home/home.sh"

host="${1:-http://localhost/pad/}"
docs="$padHome/docs/apps"
work=$(mktemp -d "$padHome/DATA/pages.XXXXXX") || exit 2

trap 'rm -rf "$work"' EXIT

php -d memory_limit=2G -- "$padHome" "$host" "$work" <<'PHP' || exit 1
<?php

  [ , $home, $host, $out ] = $argv;

  $base   = rtrim ( $host, '/' ) . '/';
  $out    = rtrim ( $out, '/' ) . '/';
  $www    = rtrim ( $home, '/' ) . '/www/';
  $parts  = parse_url ( $base );
  $origin = $parts ['scheme'] . '://' . $parts ['host'] . ( isset ( $parts ['port'] ) ? ':' . $parts ['port'] : '' );
  $root   = $parts ['path'] ?? '/';

  // The applications whose pages and files are copied.

  $apps = [ 'pad', 'manual', 'reference', 'sequence', 'examples', 'apps', 'demo', 'hello',
            'nono', 'react', 'structure', 'cli', 'classicModels', 'regression/main', 'develop',
            'tags', 'charts', 'showcase', 'alpine', 'htmx', 'islands', 'webcomponents',
            'notes', 'ledger', 'kanban' ];

  // The pages of those that act when they are fetched, by their query string: the suites'
  // test run, recording an answer, a build, the todo list's post target - and every page of
  // develop but its index, which only lists its tools. The calendar's months are left out
  // too: each links to the next, ten thousand years of them.

  $skip = [ 'develop'         => '/./',
            'regression/main' => '/^(build|record)(&|$)|(^|&)test(&|=|$)/',
            'reference'       => '/^build(&|$)/',
            'examples'        => '/^build(&|$)/',
            'demo'            => '/^todoPost(&|$)|^calendar&(.*&)?month=/' ];

  // Links a static copy cannot follow, and the page they lead to in it instead.

  $map = [ '#^$#'                        => 'pad/',
           '#^edit/\?index&app=([^&]+)$#' => 'apps/?browse&app=$1',
           '#^admin/$#'                   => 'apps/' ];

  // The href, src, poster and srcset of the tags that link or load: a and area, link,
  // script, img, iframe, frame, video, audio, source and embed. A tag shown as text in a code
  // sample is &lt;a, never matched.

  const pagesTags = '/<(?:a|area|link|script|img|iframe|frame|video|audio|source|embed)\b[^>]*>/is';
  const pagesAttr = '/(\s(href|src|poster|srcset)\s*=\s*)(["\'])(.*?)\3/is';

  $queue  = [];
  $seen   = [];
  $saved  = [];
  $alias  = [];
  $failed = [];
  $left   = [];

  pagesQueue ( 'pad/' );

  while ( $queue ) {

    foreach ( pagesFetch ( array_splice ( $queue, 0, 8 ) ) as $rel => [ $code, $type, $body, $location ] ) {

      if ( $code >= 300 and $code < 400 ) {
        $to = ( $location !== '' ) ? pagesLocal ( $location, $rel ) : NULL;
        if ( $to !== NULL and pagesKind ( $to ) ) {
          $alias [$rel] = $to;
          pagesQueue ( $to );
        } else
          $failed [$rel] = "$code to " . ( $location ?: 'nowhere' ) . ', left out';
        continue;
      }

      if ( $code != 200 ) {
        $failed [$rel] = $code ?: 'no answer';
        continue;
      }

      $html = str_starts_with ( strtolower ( $type ), 'text/html' );

      $saved [$rel] = [ 'html' => $html, 'body' => $body ];

      if ( $html )
        $links = pagesLinks ( $body );
      elseif ( preg_match ( '/\.m?js$/i', $rel ) )
        $links = pagesImports ( $body );
      else
        $links = [];

      foreach ( $links as $href )
        if ( ( $to = pagesLocal ( $href, $rel ) ) !== NULL and pagesKind ( $to ) )
          pagesQueue ( $to );

      if ( count ( $saved ) % 500 == 0 )
        echo count ( $saved ) . " fetched, " . count ( $queue ) . " to go\n";

    }

  }

  // A second page under a name already taken - two spellings of one query - is left out.

  $names = [];

  foreach ( $saved as $rel => $one ) {
    $name = pagesName ( $rel, $one ['html'] );
    if ( in_array ( $name, $names, TRUE ) ) {
      $failed [$rel] = 'same name as ' . array_search ( $name, $names, TRUE ) . ', left out';
      unset ( $saved [$rel] );
    } else
      $names [$rel] = $name;
  }

  foreach ( $saved as $rel => $one ) {

    $file = $out . $names [$rel];
    $dir  = dirname ( $names [$rel] );
    $dir  = ( $dir === '.' ) ? '' : "$dir/";

    if ( ! is_dir ( dirname ( $file ) ) )
      mkdir ( dirname ( $file ), 0755, TRUE );

    file_put_contents ( $file, $one ['html'] ? pagesRewrite ( $one ['body'], $rel, $dir ) : $one ['body'] );

  }

  if ( isset ( $saved ['pad/'] ) )
    file_put_contents ( $out . 'index.html', pagesRewrite ( $saved ['pad/'] ['body'], 'pad/', '' ) );

  $pages = count ( array_filter ( $saved, fn ( $one ) => $one ['html'] ) );

  echo "$pages pages and " . ( count ( $saved ) - $pages ) . " files\n";

  foreach ( $failed as $rel => $why )
    echo "  $why  $base$rel\n";

  if ( $left ) {
    arsort ( $left );
    echo "links left pointing at the server: " . implode ( ', ', array_map ( fn ( $k, $v ) => "$k ($v)", array_keys ( $left ), $left ) ) . "\n";
  }

  if ( ! isset ( $saved ['pad/'] ) )
    exit ( 1 );

  // Queues a page or file once.

  function pagesQueue ( $rel ) {

    global $queue, $seen;

    if ( isset ( $seen [$rel] ) )
      return;

    $seen  [$rel] = TRUE;
    $queue []     = $rel;

  }

  // Fetches up to eight pages at once: [ rel => [ code, type, body, redirect ] ].

  function pagesFetch ( $rels ) {

    global $origin, $root;

    $multi   = curl_multi_init ();
    $handles = [];
    $result  = [];

    foreach ( $rels as $rel ) {

      $url  = $origin . $root . preg_replace_callback ( '/[^\x21-\x7E]/', fn ( $m ) => rawurlencode ( $m [0] ), $rel );
      $curl = curl_init ( $url );

      curl_setopt_array ( $curl, [ CURLOPT_RETURNTRANSFER => TRUE,
                                   CURLOPT_FOLLOWLOCATION => FALSE,
                                   CURLOPT_TIMEOUT        => 120,
                                   CURLOPT_ENCODING       => '',
                                   CURLOPT_USERAGENT      => 'PAD pages.sh',
                                   CURLOPT_HTTPHEADER     => [ 'X-Forwarded-For: 203.0.113.7' ] ] );

      curl_multi_add_handle ( $multi, $curl );

      $handles [$rel] = $curl;

    }

    do {
      $status = curl_multi_exec ( $multi, $active );
      if ( $active )
        curl_multi_select ( $multi );
    } while ( $active and $status == CURLM_OK );

    foreach ( $handles as $rel => $curl ) {
      $result [$rel] = [ (int) curl_getinfo ( $curl, CURLINFO_RESPONSE_CODE ), (string) curl_getinfo ( $curl, CURLINFO_CONTENT_TYPE ),
                         (string) curl_multi_getcontent ( $curl ), (string) curl_getinfo ( $curl, CURLINFO_REDIRECT_URL ) ];
      curl_multi_remove_handle ( $multi, $curl );
    }

    curl_multi_close ( $multi );

    return $result;

  }

  function pagesLinks ( $html ) {

    $links = [];

    preg_match_all ( pagesTags, $html, $tags );

    foreach ( $tags [0] as $tag )
      if ( preg_match_all ( pagesAttr, $tag, $attrs, PREG_SET_ORDER ) )
        foreach ( $attrs as $attr )
          if ( strtolower ( $attr [2] ) == 'srcset' )
            array_push ( $links, ...pagesSrcset ( $attr [4] ) );
          else
            $links [] = $attr [4];

    return $links;

  }

  // The pictures of a srcset: the candidates split at a comma, each one's width or density
  // behind a space left off.

  function pagesSrcset ( $srcset ) {

    preg_match_all ( '/(?:^|,)\s*([^\s,]+)/', $srcset, $found );

    return $found [1];

  }

  // The modules a script names by a relative path in quotes - import ... from './x.js',
  // import ( './x.js' ), the files of a Vite build's preload list, styles among them.

  function pagesImports ( $script ) {

    preg_match_all ( '/(["\'`])(\.\.?\/[^"\'`\s]+?\.(?:m?js|css))\1/', $script, $found );

    return array_unique ( $found [2] );

  }

  // A link as the path below the site's root - manual/?pages/forms, demo/style.css - or
  // NULL for a link to another site, a bare #anchor, mailto: and the like. $from is the
  // page the link stands in.

  function pagesLocal ( $href, $from ) {

    global $origin, $root, $www, $map;

    $href = trim ( explode ( '#', html_entity_decode ( $href, ENT_QUOTES | ENT_HTML5, 'UTF-8' ), 2 ) [0] );

    if ( $href === '' or str_starts_with ( $href, '//' ) )
      return NULL;

    if ( preg_match ( '#^[a-z][a-z0-9+.-]*:#i', $href ) ) {
      if ( ! str_starts_with ( $href, "$origin/" ) )
        return NULL;
      $abs = substr ( $href, strlen ( $origin ) );
    } elseif ( $href [0] == '/' )
      $abs = $href;
    else {
      $page = $root . explode ( '?', $from, 2 ) [0];
      $abs  = ( $href [0] == '?' ) ? $page . $href : substr ( $page, 0, strrpos ( $page, '/' ) + 1 ) . $href;
    }

    [ $path, $query ] = array_pad ( explode ( '?', $abs, 2 ), 2, '' );

    $path = pagesDots ( $path );

    if ( ! str_starts_with ( $path, $root ) and $path . '/' !== $root )
      return NULL;

    $path = (string) substr ( $path, strlen ( $root ) );
    $path = preg_replace ( '#(^|/)index\.php$#', '$1', $path );

    if ( $path !== '' and ! str_ends_with ( $path, '/' ) and is_file ( "$www$path/index.php" ) )
      $path .= '/';

    // A file is the same file whatever its query - a ?v= only names its version.

    if ( preg_match ( '/\.(?!php$)[A-Za-z0-9]+$/i', $path ) )
      $query = '';

    $rel = $path . ( $query === '' ? '' : "?$query" );

    foreach ( $map as $pattern => $to )
      $rel = preg_replace ( $pattern, $to, $rel );

    return $rel;

  }

  // A path with its . and .. segments taken out.

  function pagesDots ( $path ) {

    $done = [];

    foreach ( explode ( '/', $path ) as $i => $part )
      if ( $part === '..' )
        array_pop ( $done );
      elseif ( $part !== '.' or $i == 0 )
        $done [] = $part;

    if ( str_ends_with ( $path, '/.' ) or str_ends_with ( $path, '/..' ) )
      $done [] = '';

    return implode ( '/', $done );

  }

  // 'page' or 'asset' for what the copy takes, NULL for what it leaves out: outside the
  // listed applications, a .php file, a page that acts.

  function pagesKind ( $rel ) {

    global $apps, $skip;

    [ $path, $query ] = array_pad ( explode ( '?', $rel, 2 ), 2, '' );

    $app = NULL;

    foreach ( $apps as $one )
      if ( str_starts_with ( $path, "$one/" ) and strlen ( $one ) > strlen ( (string) $app ) )
        $app = $one;

    if ( $app === NULL )
      return NULL;

    if ( preg_match ( '/\.([A-Za-z0-9]+)$/', $path, $match ) )
      return ( strtolower ( $match [1] ) == 'php' ) ? NULL : 'asset';

    if ( isset ( $skip [$app] ) and preg_match ( $skip [$app], $query ) )
      return NULL;

    return 'page';

  }

  // The name SiteSucker gave a page: the path, index for a directory, then the query -
  // decoded, a + left as it is - behind ﹖ with its slashes as ꤷ, and .html for a page.

  function pagesName ( $rel, $html ) {

    [ $path, $query ] = array_pad ( explode ( '?', $rel, 2 ), 2, '' );

    if ( $path === '' or str_ends_with ( $path, '/' ) )
      $path .= 'index';

    $query = str_replace ( [ '/', '?' ], [ "\u{A937}", "\u{FE56}" ], rawurldecode ( $query ) );
    $name  = $path . ( $query === '' ? '' : "\u{FE56}$query" );

    return ( $html and ! str_ends_with ( $name, '.html' ) ) ? "$name.html" : $name;

  }

  // The links of a page pointed at the copies, relative to the directory the page is
  // written to - in a srcset each picture's; a link with no copy is left as it is.

  function pagesRewrite ( $html, $rel, $dir ) {

    return preg_replace_callback ( pagesTags, function ( $tag ) use ( $rel, $dir ) {

      return preg_replace_callback ( pagesAttr, function ( $attr ) use ( $rel, $dir ) {

        if ( strtolower ( $attr [2] ) == 'srcset' )
          $value = preg_replace_callback ( '/((?:^|,)\s*)([^\s,]+)/',
                                           fn ( $one ) => $one [1] . ( pagesCopy ( $one [2], $rel, $dir ) ?? $one [2] ), $attr [4] );
        else
          $value = pagesCopy ( $attr [4], $rel, $dir ) ?? $attr [4];

        return $attr [1] . $attr [3] . $value . $attr [3];

      }, $tag [0] );

    }, $html );

  }

  // A link as the relative path of its copy, written for an attribute, or NULL when it has
  // none - a link to the server is counted by where it leads.

  function pagesCopy ( $href, $rel, $dir ) {

    global $names, $alias, $left;

    $to = pagesLocal ( $href, $rel );

    for ( $hop = 0; $to !== NULL and isset ( $alias [$to] ) and $hop < 10; $hop++ )
      $to = $alias [$to];

    if ( $to === NULL )
      return NULL;

    if ( ! isset ( $names [$to] ) ) {
      $where = explode ( '/', explode ( '?', $to, 2 ) [0] );
      $where = ( $where [0] == 'regression' and isset ( $where [1] ) ) ? "regression/$where[1]/" : "$where[0]/";
      $left [$where] = ( $left [$where] ?? 0 ) + 1;
      return NULL;
    }

    $hash = str_contains ( $href, '#' ) ? '#' . explode ( '#', $href, 2 ) [1] : '';

    return htmlspecialchars ( pagesPath ( $dir, $names [$to] ), ENT_QUOTES ) . $hash;

  }

  // The relative path from a directory of the copy to a file of it, each part encoded as
  // far as a path needs - ﹖ and ꤷ are, & = + stay readable, as SiteSucker wrote them.

  function pagesPath ( $dir, $name ) {

    $from = ( $dir === '' ) ? [] : explode ( '/', rtrim ( $dir, '/' ) );
    $to   = explode ( '/', $name );

    while ( $from and count ( $to ) > 1 and $from [0] === $to [0] ) {
      array_shift ( $from );
      array_shift ( $to );
    }

    $encode = fn ( $part ) => preg_replace_callback ( "/[^A-Za-z0-9._~!$&'()*+,;=:@-]/", fn ( $m ) => rawurlencode ( $m [0] ), $part );

    return str_repeat ( '../', count ( $from ) ) . implode ( '/', array_map ( $encode, $to ) );

  }

PHP

chmod 755 "$work"
rm -rf "$docs"
mv "$work" "$docs"

echo "docs/apps rebuilt from $host"
