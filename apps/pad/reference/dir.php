<?php

  if ( ! isset ( $for    ) ) $for    = 'tags';
  if ( ! isset ( $xitem  ) ) $xitem  = 'pad';

  $title = "$for - $xitem";
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
                    'xitem' => $xitem,
                    'for'   => $for ] );
 
?>