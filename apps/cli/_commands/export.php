<?php

  // pad export <app> <dir>: a static copy of an application, for any static host.
  //
  // Every page the suites would walk is rendered as the web gets it - pad render with
  // PAD_EXPORT, four child processes at a time, no toolbar and no live reload - and written
  // as <page>.html, a page in a subdirectory as <dir>/<page>.html. The links of each page
  // are rewritten on the way: ?page, the application's own /app/?page and its absolute
  // form become the relative page.html when that page was exported; a link to a file of the
  // application (style.css) becomes the relative path to its copy, so a page one directory
  // down still finds it. Query values after the page name cannot be static, so a link with
  // them lands on the page as it renders without them. The assets are what www/<app>/ holds
  // beside the entry point - stylesheets, scripts, images, data files - copied less the
  // .php files, the _ and . names, and the directories that are applications of their own.
  //
  // A page that fails, or answers with nothing (a redirect), is reported and left out. Exit
  // status 1 when any page failed. PAD_HOST, when set, is the server the pages render
  // against; the absolute links that point at it are rewritten too.

  $exportApp = trim ( $argv [2] ?? '', '/' );
  $exportDir = rtrim ( $argv [3] ?? '', '/' );

  if ( ! cliApp ( $exportApp ) )
    return cliFail ( "there is no application named '$exportApp' - pad export <app> <dir>" );

  if ( $exportDir === '' )
    return cliFail ( "pad export <app> <dir> - the directory to write the copy to" );

  if ( ! is_dir ( $exportDir ) and ! mkdir ( $exportDir, 0755, TRUE ) )
    return cliFail ( "cannot make the directory $exportDir" );

  $exportPages   = cliPages ( $exportApp );
  $exportResult  = cliRunPages ( $exportApp, $exportPages, [ 'PAD_EXPORT' => '1' ] );
  $exportWritten = [];
  $exportFailed  = 0;

  foreach ( $exportResult as $exportPage => list ( $exportCode, $exportOut ) )
    if ( $exportCode === 0 and trim ( $exportOut ) !== '' )
      $exportWritten [$exportPage] = $exportOut;
    else {
      $exportFailed++;
      $exportJson = json_decode ( trim ( $exportOut ), TRUE );
      cliOut ( "skipped $exportPage - " . ( is_array ( $exportJson ) ? preg_replace ( '/^PAD: /', '', (string) ( $exportJson ['error'] ?? '' ) )
                                                                     : ( $exportCode === 0 ? 'no output' : "exit $exportCode" ) ) );
    }

  foreach ( $exportWritten as $exportPage => $exportOut ) {

    $exportFile = "$exportDir/$exportPage.html";

    if ( ! is_dir ( dirname ( $exportFile ) ) )
      mkdir ( dirname ( $exportFile ), 0755, TRUE );

    file_put_contents ( $exportFile, exportLinks ( $exportOut, $exportPage, array_keys ( $exportWritten ), $exportApp ) );

  }

  $exportAssets = exportAssets ( cliHome () . "/www/$exportApp", $exportDir, '' );

  cliOut ( count ( $exportWritten ) . " pages and $exportAssets assets exported to $exportDir"
         . ( $exportFailed ? ", $exportFailed pages skipped" : '' ) );

  return $exportFailed ? 1 : 0;


  // The links of one exported page, rewritten for the static copy.

  function exportLinks ( $html, $page, $pages, $app ) {

    $host  = rtrim ( getenv ( 'PAD_HOST' ) ?: 'http://localhost/', '/' ) . '/';
    $root  = '/' . ltrim ( (string) parse_url ( $host, PHP_URL_PATH ), '/' );
    $bases = [ "$host$app/", "$root$app/" ];
    $from  = str_contains ( $page, '/' ) ? substr ( $page, 0, strrpos ( $page, '/' ) ) : '';

    return preg_replace_callback (

      '/\b(href|src|action)(\s*=\s*)(["\'])(.*?)\3/is',

      function ( $m ) use ( $bases, $pages, $from ) {

        $url  = $m [4];
        $rest = NULL;

        foreach ( $bases as $base )
          if ( str_starts_with ( $url, $base ) or $url === rtrim ( $base, '/' ) ) {
            $rest = (string) substr ( $url, strlen ( $base ) );
            break;
          }

        // Not the application's own absolute or root-relative form: a scheme, a //host, a
        // path from another root, a bare #anchor - left as they are. A plain relative link
        // is relative to the application's root, which is where the web serves its pages.

        if ( $rest === NULL ) {
          if ( $url === '' or $url [0] == '#' or $url [0] == '/' or preg_match ( '/^[a-z][a-z0-9+.-]*:/i', $url ) )
            return $m [0];
          $rest = $url;
        }

        list ( $rest, $anchor ) = array_pad ( explode ( '#', $rest, 2 ), 2, NULL );
        $anchor = ( $anchor === NULL ) ? '' : "#$anchor";

        if ( $rest === '' or $rest === './' or str_starts_with ( $rest, '?' ) ) {

          $target = ( $rest === '' or $rest === './' ) ? 'index' : html_entity_decode ( substr ( $rest, 1 ) );
          $target = preg_split ( '/[&=]/', $target ) [0];
          $target = ( $target === '' ) ? 'index' : trim ( $target, '/' );

          if     ( in_array ( $target,          $pages, TRUE ) ) $file = "$target.html";
          elseif ( in_array ( "$target/index",  $pages, TRUE ) ) $file = "$target/index.html";
          else   return $m [0];

        } else

          $file = $rest;

        return $m [1] . $m [2] . $m [3] . htmlspecialchars ( exportRelative ( $from, $file ), ENT_QUOTES ) . $anchor . $m [3];

      },

      $html

    );

  }

  // The path from the directory a page sits in to a file, both from the export root.

  function exportRelative ( $from, $file ) {

    $up = ( $from === '' ) ? 0 : substr_count ( $from, '/' ) + 1;

    return str_repeat ( '../', $up ) . $file;

  }

  // The files beside the entry point, copied; returns how many.

  function exportAssets ( $dir, $to, $prefix ) {

    $count = 0;

    if ( ! is_dir ( $dir ) )
      return 0;

    foreach ( scandir ( $dir ) as $file ) {

      if ( str_starts_with ( $file, '.' ) or str_starts_with ( $file, '_' ) )
        continue;

      if ( is_dir ( "$dir/$file" ) ) {
        if ( ! file_exists ( "$dir/$file/index.php" ) )
          $count += exportAssets ( "$dir/$file", $to, "$prefix$file/" );
        continue;
      }

      if ( str_ends_with ( $file, '.php' ) )
        continue;

      if ( ! is_dir ( "$to/$prefix" ) )
        mkdir ( "$to/$prefix", 0755, TRUE );

      copy ( "$dir/$file", "$to/$prefix$file" );

      $count++;

    }

    return $count;

  }

?>
