<?php

  if ( ! isset ( $xref ) )
    $xref = '';

  $title = 'cross reference';
  if ( $xref )
    $title .= ' - ' . str_replace ('/', ' ', $xref);

  $dirs = [];
  $pages = [];

  $list = scandir ( padApp . 'xref' . $xref );

  foreach ( $list as $file )
    if     ( $file == '.'                    ) continue;
    elseif ( $file == '..'                   ) continue;
    elseif ( str_ends_with ( $file, '.hit' ) ) $pages [$file] ['page'] = substr (str_replace ('@', '/', $file), 0, -4);
    else                                       $dirs  [$file] ['dir']  = $file; 

?>