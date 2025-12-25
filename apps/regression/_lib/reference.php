<?php

  function getXref ( $dir, $xref,  ) {

    if ( ! $dir )
      return [];

    if ( $dir == '*code*' ) {
      $items = array_merge ( getXref ( 'eval/single', $xref ), getXref ( 'eval/parms',  $xref ) );
      ksort ($items);
      return $items;
    }

    if ( $xref == '*' ) {
      $base  = APP . "reference/DATA/$dir/";
      $check = APP . "reference/DATA/$dir/";
    } else {
      $base  = PAD . "$dir/";
      $check = APP . "reference/DATA/$xref/";
    }

    $items = [];

    foreach ( padItems ( $base ) as $one ) {

      extract ( $one );

      $item = str_replace ( "$dir/", '', $item );

      if ( ( $xref == 'tag'       and $dir == 'types'       and file_exists ( PQ . "start/types/$item.php" ) )
        or ( $xref == 'properties/pad'   and $dir == 'tags'        and file_exists ( PQ . "start/tags/$item.php"  ) )
        or ( $xref == 'functions' and $dir == 'eval/single' and file_exists ( PQ . "start/eval/$item.php"  ) )
        or ( $xref == 'functions' and $dir == 'eval/parms'  and file_exists ( PQ . "start/eval/$item.php"  ) ) )
        continue;

      if ( $item ) {
        $items [$item] ['item']  = $item;
        $items [$item] ['dir']   = ( is_dir      ( "$check$item"     ) ) ? "$xref/$item"     : '';
        $items [$item] ['pages'] = ( file_exists ( "$check$item.txt" ) ) ? "$xref/$item.txt" : '';
      }

    }

    return $items;

  }

?>