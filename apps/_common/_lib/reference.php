<?php

  function getReference ( $dir, $xref ) {

    if ( ! $dir )
      return [];

    $items = [];

    foreach ( scandir ( PAD . $dir ) as $file ) {

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