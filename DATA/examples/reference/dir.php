<?php

  if ( ! isset ( $type ) ) $type = 'Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag';
  if ( ! isset ( $item ) ) $item = 'pad';

  foreach ( padFiles ( DAT . "reference/$xref/$item" ) as $file ) 
    $hits [$file] ['item'] = str_replace ( '.txt', '', $file );

  $xref = "$xref/$item";

  if ( count ($hits) == 1 )
    padRedirect ( 'pages',
                  [ 'type' => $type,
                    'xref' => $xref,
                    'item' => $hits [array_key_first($hits)] ['item'] ] );

  $title .= " - $type - $item";

?>