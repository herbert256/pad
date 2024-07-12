<?php

  if ( ! isset ( $dir  ) ) $dir  = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'pad';
  if ( ! isset ( $type ) ) $type = 'Tags';

  $list = scandir ( padApp . "_xapp/$dir" );

  foreach ( $list as $file ) {

    if ( $file == '.' or $file == '..' ) 
      continue;
  
    $hits [$file] ['dir']   = '';
    $hits [$file] ['item' ] = str_replace ( '.txt', '', $file );
    $hits [$file] ['pages'] = "$dir/$file";

  }
 
  if ( count ($hits) == 1 )
      padRedirect ( $go = '_xapp/pages',
                    [ 'pages' => $hits [$file] ['pages'],
                      'type'  => $type ?? '',
                      'item'  => $item ?? ''  ] );

  $title .= ' - $type - $item";

?>