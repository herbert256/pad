<?php

  foreach ( files ( $xDir ) as $file ) {

    $hits [$file] ['item'  ] = str_replace ( '.txt', '', $file );
    $hits [$file] ['pages' ] = "$dir/$file";

    }

  if ( count ($hits) == 1 )
      padRedirect ( 'reference/pages',
                    [ 'pages' => $hits [$file] ['pages'],
                      'type'  => $type,
                      'item'  => $item ] );

  $title .= " - $type - $item";
  $type   = "$type - $item";

?>