<?php

  if ( ! isset ( $xref ) ) $xref = '';
  if ( ! isset ( $go   ) ) $go = '';

  $title = 'cross reference';

  if ( $xref ) $title .= ' - ' . substr ( str_replace ('/', '-', $xref), 1 );
  if ( $go   ) $title .= ' - ' . $go;

  $dirs = [];
  $pages = [];

  $list = scandir ( padApp . 'xref' . $xref );

  foreach ( $list as $file )
    if     ( $file == '.'                    ) continue;
    elseif ( $file == '..'                   ) continue;
    elseif ( str_ends_with ( $file, '.hit' ) ) $pages [$file] ['page'] = substr (str_replace ('@', '/', $file), 0, -4);
    else                                       $dirs  [$file] ['dir']  = $file; 

?>