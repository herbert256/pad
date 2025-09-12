<?php

  if ( ! isset ( $dir  ) ) $dir  = 'tag/pad';
  if ( ! isset ( $type ) ) $type = 'Tags';
  if ( ! isset ( $item ) ) $item = 'pad';

  $list = scandir ( APP . "_xref/$dir" );

  foreach ( $list as $file ) {

    if ( $file == '.' or $file == '..' ) 
      continue;
  
    $hits [$file] ['item'  ] = str_replace ( '.txt', '', $file );
    $hits [$file] ['pages' ] = "$dir/$file";

    }
 
  if ( count ($hits) == 1 )
      padRedirect ( 'xref/pages',
                    [ 'pages' => $hits [$file] ['pages'],
                      'type'  => $type,
                      'item'  => $item ] );

  $title .= " - $type - $item";
  $type   = "$type - $item";

?>