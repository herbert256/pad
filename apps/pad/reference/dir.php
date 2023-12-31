<?php

  if ( ! isset ( $dir  ) ) $dir   = 'tag';
  if ( ! isset ( $for  ) ) $for   = 'tags';
  if ( ! isset ( $item ) ) $item  = 'pad';

  $base = $item;

  $title = "$for - $item";
  $list  = scandir ( padApp . '_xref/manual/' . $dir );

  foreach ( $list as $file ) {

    if ( $file == '.' or $file == '..' ) 
      continue;

    $hits [$file] ['item']  = str_replace ( '.txt', '', $file ) ; 
    $hits [$file] ['pages'] = "$dir/$file"; 
  
  }
 
  if ( count ($hits) == 1 )
    padRedirect ( $go = 'reference/pages',
                  [ 'pages' => $hits [$file] ['pages'],
                    'item' => $item,
                    'for'   => $for ] );
 
?>