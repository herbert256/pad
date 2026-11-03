<?php

  // A usage crawl can only record features the example pages execute. The catalogue is
  // larger: PHP helpers, service providers and wrapper fragments also have a reference.
  // Resolve those against the catalogue, never a path supplied by the request, so a new
  // entry remains readable before the next harvest and without inventing a usage record.

  function referenceEntry ( $xref, $item ) {

    if ( ! is_string ( $xref ) or ! is_string ( $item ) ) return [];

    foreach ( json_decode ( file_get_contents ( APPS . 'reference/_data/reference.json' ), TRUE ) as $section ) {

      if ( ! $section ['dir'] or $section ['xref'] !== $xref ) continue;
      if ( ! isset ( getReference ( $section ['dir'], $xref ) [$item] ) ) continue;

      $common = str_starts_with ( $section ['dir'], 'common:' );
      $dir = $common ? 'apps/_common/' . substr ( $section ['dir'], 7 ) : 'pad/' . $section ['dir'];
      $root = $common ? COMMON . substr ( $section ['dir'], 7 ) : PAD . $section ['dir'];
      $files = [];

      foreach ( [ '.php', '.pad', '.svg' ] as $extension )
        if ( is_file ( "$root/$item$extension" ) )
          $files ["$dir/$item$extension"] = file_get_contents ( "$root/$item$extension" );

      // Info modes and several engine handlers are directories. Their entry points give
      // the useful description; info also has a corresponding configuration selector.
      if ( is_dir ( "$root/$item" ) ) {
        if ( $xref === 'config/info' and is_file ( PAD . "config/info/$item.php" ) )
          $files ["pad/config/info/$item.php"] = file_get_contents ( PAD . "config/info/$item.php" );
        foreach ( [ 'index.php', 'start.php', 'end.php' ] as $file )
          if ( is_file ( "$root/$item/$file" ) )
            $files ["$dir/$item/$file"] = file_get_contents ( "$root/$item/$file" );
      }

      return [ 'type' => $section ['type'], 'files' => $files ];

    }

    return [];

  }

?>
