<?php

  if ( ! isset ( $dir  ) ) $dir  = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'pad';
  if ( ! isset ( $for  ) ) $for  = 'Tags';
  if ( ! isset ( $base ) ) $base  = '';

  $list = scandir ( padApp . "_xweb/$dir" );

  foreach ( $list as $file ) {

    if ( $file == '.' or $file == '..' ) 
      continue;
  
    $hits [$file] ['dir']   = '';
    $hits [$file] ['pages'] = '';
    $hits [$file] ['item' ] = str_replace ( '.txt', '', $file );

    if ( str_ends_with($file, '.txt' ) )
      $hits [$file] ['pages'] = "$dir/$file";
    else
      $hits [$file] ['dir'] = "$dir/$file";

  }
 
  if ( count ($hits) == 1 )
    if ( $hits [$file] ['pages'] )
      padRedirect ( $go = '_xweb/pages',
                    [ 'pages' => $hits [$file] ['pages'],
                      'for'   => $for ?? '',
                      'item'  => $item ?? '',
                      'base'  => $base ?? ''] );
    else 
      padRedirect ( $go = '_xweb/dir',
                    [ 'dir  ' => $hits [$file] ['dir'],
                      'for'   => $for ?? '',
                      'base'  => $file] );

                     $title  = 'Reference';
  if ( isset($for) ) $title .= " - $for";
                     $title .= " - $item";

?>