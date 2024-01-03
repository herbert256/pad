<?php

  if ( ! isset ( $xref ) ) $xref = 'manual';
  if ( ! isset ( $dir  ) ) $dir  = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'pad';
  if ( ! isset ( $for  ) ) $for  = 'Tags';
  if ( ! isset ( $base ) ) $base  = '';

  $list = scandir ( padApp . "_xref/$xref/$dir" );

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
      padRedirect ( $go = 'xref/pages',
                    [ 'pages' => $hits [$file] ['pages'],
                      'for'   => $for ?? '',
                      'item'  => $item ?? '',
                      'base'  => $base ?? '',
                      'xref'  => $xref] );
    else 
      padRedirect ( $go = 'xref/dir',
                    [ 'dir  ' => $hits [$file] ['dir'],
                      'for'   => $for ?? '',
                      'base'  => $file,
                      'xref'  => $xref] );

                     $title  = 'Reference';
  if ( isset($for) ) $title .= " - $for";
                     $title .= " - $item";

?>