<?php

  if ( ! isset ( $go    ) ) $go   = '';
  if ( ! isset ( $first  ) ) $first = '';
  if ( ! isset ( $second ) ) $second = '';
  if ( ! isset ( $xitem  ) ) $xitem = '';

  if ( ! isset ($for) )
    return;

  $title = $for;

  if ( $xitem  ) $title .= " - $xitem";
  if ( $second ) $title .= " - $second";

  if ( $go ) {

go: $source = padFileGetContents ( padApp . $go . '.pad' );

    $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

    $title .= " - $go";
    
    return TRUE;
 
  }

  $dirs  = [];
  $pages = [];

  $xref = $first;
  if ($second)
    $xref .= "/$second";

  $list = scandir ( padApp . '_xref' . $xref );

  foreach ( $list as $file )
    if     ( $file == '.'                    ) continue;
    elseif ( $file == '..'                   ) continue;
    elseif ( str_ends_with ( $file, '.hit' ) ) $pages [$file] ['page'] = substr (str_replace ('@', '/', $file), 0, -4);
    else                                       $dirs  [$file] ['dir']  = $file; 

  if ( count ($pages) == 1 ) {
    $go = $pages [$file] ['page'] ;
    goto go;
  }

?>