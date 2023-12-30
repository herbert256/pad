<?php

  if ( ! isset ( $for    ) ) $for    = 'tags';
  if ( ! isset ( $first  ) ) $first  = '';
  if ( ! isset ( $second ) ) $second = '';
  if ( ! isset ( $xitem  ) ) $xitem  = '';

                 $title  = $for;
  if ( $xitem  ) $title .= " - $xitem";
  if ( $second ) $title .= " - $second";

  $dirs = $pages = [];

  $xref = $first;
  if ($second)
    $xref .= "/$second";

  $list = scandir ( padApp . '_xref/manual' . $xref );

  foreach ( $list as $file )
    if     ( $file == '.'                    ) continue;
    elseif ( $file == '..'                   ) continue;
    elseif ( str_ends_with ( $file, '.hit' ) ) $pages [$file] ['page'] = substr (str_replace ('@', '/', $file), 0, -4);
    else                                       $dirs  [$file] ['dir']  = $file; 

  if ( count ($pages) == 1 )
    padRedirect ( $go = 'reference/go',
                  [ 'go'     => $pages [$file] ['page'],
                    'first'  => $first,
                    'second' => $second,
                    'xitem'  => $xitem,
                    'for'    => $for ] );
 
?>