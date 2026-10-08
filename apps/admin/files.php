<?php

  // The files of an application - apps/<app>/, or its www/<app>/ - as a tree beside the
  // file asked for, coloured by its kind. Reading only: the editor is where they change.
  //
  // What is never shown: a name that starts with a dot (.env holds the passwords padEnv
  // reads), a file that is no text, and in what is shown, the values of passwords, keys
  // and secrets (adminRedact).

  global $adminMaxFile;

  $app  = adminAppAsked ();
  $root = adminGet ( 'root' ) === 'www' ? 'www' : 'app';
  $dir  = adminRelative ( adminGet ( 'dir' ) );
  $file = adminRelative ( adminGet ( 'file' ) );

  $title    = $app === '' ? 'Files' : "Files of $app";
  $appRows  = [];

  foreach ( adminApps () as $name => $one )
    $appRows [] = [ 'name' => $name, 'selected' => $name === $app ? 1 : 0 ];

  $base     = '';
  $hasWww   = 0;
  $dirRows  = $fileRows = $crumbs = [];
  $shown    = '';
  $shownAs  = '';
  $fileName = '';
  $fileInfo = '';
  $notShown = '';
  $rawLink  = '';

  $textKinds = [ 'pad' => 'pad', 'php' => 'php', 'html' => 'html', 'htm' => 'html', 'xml' => 'html', 'svg' => 'html',
                 'css' => 'css', 'js' => 'js', 'mjs' => 'js', 'ts' => 'js', 'json' => 'json', 'yaml' => 'yaml',
                 'yml' => 'yaml', 'sql' => 'sql', 'sh' => 'bash', 'md' => 'md', 'txt' => 'text', 'csv' => 'text',
                 'curl' => 'html', 'eml' => 'text', 'ini' => 'text', 'log' => 'text', 'jsonl' => 'text', 'tsx' => 'js',
                 'jsx' => 'js', 'vue' => 'html', 'svelte' => 'html' ];

  if ( $app !== '' ) {

    $hasWww = adminApps () [$app] ['www'] !== '' ? 1 : 0;

    if ( $root == 'www' and ! $hasWww )
      $root = 'app';

    $base = $root == 'www' ? adminHome () . "www/$app/" : APPS . "$app/";

    // A file asked for without its directory opens in it.

    if ( $file !== '' and adminGet ( 'dir', NULL ) === NULL )
      $dir = str_contains ( $file, '/' ) ? dirname ( $file ) : '';

    if ( $dir !== '' and ( ! is_dir ( $base . $dir ) or adminNestedApp ( $base . $dir ) and $root == 'app' ) )
      $dir = '';

    // The crumbs above the tree: the root, then each directory down to this one.

    $crumbs [] = [ 'label' => $root == 'www' ? "www/$app" : "apps/$app", 'dir' => '' ];

    $walked = '';

    foreach ( $dir === '' ? [] : explode ( '/', $dir ) as $part ) {
      $walked .= ( $walked === '' ? '' : '/' ) . $part;
      $crumbs [] = [ 'label' => $part, 'dir' => $walked ];
    }

    foreach ( scandir ( $base . ( $dir === '' ? '' : "$dir/" ) ) ?: [] as $item ) {

      if ( $item [0] === '.' )
        continue;

      $rel  = ( $dir === '' ? '' : "$dir/" ) . $item;
      $path = $base . $rel;

      if ( is_dir ( $path ) ) {
        if ( $root == 'www' or ! adminNestedApp ( $path ) )
          $dirRows [] = [ 'name' => $item, 'path' => $rel ];
      } else
        $fileRows [] = [ 'name' => $item, 'path' => $rel, 'size' => adminBytes ( (int) @filesize ( $path ) ),
                         'current' => $rel === $file ? 1 : 0 ];

    }

    usort ( $dirRows,  fn ( $a, $b ) => strcasecmp ( $a ['name'], $b ['name'] ) );
    usort ( $fileRows, fn ( $a, $b ) => strcasecmp ( $a ['name'], $b ['name'] ) );

    $parentDir = str_contains ( $dir, '/' ) ? dirname ( $dir ) : '';
    $hasParent = $dir !== '' ? 1 : 0;

    // The file: within the root (no link leads out), text of a kind there is, not too big.

    if ( $file !== '' and is_file ( $base . $file ) ) {

      $real = realpath ( $base . $file );
      $ext  = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );

      $fileName = $file;
      $fileInfo = adminBytes ( filesize ( $real ) ) . ', changed ' . adminWhen ( filemtime ( $real ) )
                . ' (' . adminAgo ( filemtime ( $real ) ) . ')';

      if ( ! $real or ! str_starts_with ( $real, realpath ( $base ) . '/' ) )
        $notShown = 'This file lies outside the application.';
      elseif ( ! isset ( $textKinds [$ext] ) )
        $notShown = "A .$ext file is not shown as text.";
      elseif ( filesize ( $real ) > $adminMaxFile )
        $notShown = 'This file is larger than ' . adminBytes ( $adminMaxFile ) . ' - open it in the editor.';
      else {

        $text = adminRedact ( str_replace ( "\r\n", "\n", (string) file_get_contents ( $real ) ) );
        $kind = $textKinds [$ext];

        if ( $kind == 'md' and adminGet ( 'as' ) !== 'text' ) {
          $shown   = padMarkdown ( $text );
          $shownAs = 'markdown';
        } elseif ( $kind == 'text' or $kind == 'md' ) {
          $shown   = '<pre>' . htmlspecialchars ( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '</pre>';
          $shownAs = 'text';
        } else {
          $shown   = padHighlightStyle () . '<pre class="pad-highlight hl-numbered"><code>'
                   . padHighlightLines ( padHighlightTokens ( rtrim ( $text ), $kind ), TRUE, [] ) . '</code></pre>';
          $shownAs = $kind;
        }

      }

    }

  }

  $isMarkdown = $shownAs === 'markdown' ? 1 : 0;
  $isMdText   = ( $shownAs === 'text' and str_ends_with ( $fileName, '.md' ) ) ? 1 : 0;

?>
