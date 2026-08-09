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

    }

    return $items;

  }

?>