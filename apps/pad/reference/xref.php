<?php

  if ( ! isset ( $xgo   ) ) $xgo   = '';
  if ( ! isset ( $xmain ) ) $xmain = '';
  if ( ! isset ( $xitem ) ) $xitem = '';
  if ( ! isset ( $xnext ) ) $xnext = '';

  $title = $for;

  if ( $xitem ) $title .= " - $xitem";
  if ( $xnext ) $title .= " - $xnext";

  if ( $xgo ) {

go: $source = padFileGetContents ( padApp . $xgo . '.pad' );

    $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

    $title .= " - $xgo";
    
    return TRUE;
 
  }

  $dirs  = [];
  $pages = [];

  $list = scandir ( padApp . 'xref' . $xref );

  foreach ( $list as $file )
    if     ( $file == '.'                    ) continue;
    elseif ( $file == '..'                   ) continue;
    elseif ( str_ends_with ( $file, '.hit' ) ) $pages [$file] ['page'] = substr (str_replace ('@', '/', $file), 0, -4);
    else                                       $dirs  [$file] ['dir']  = $file; 

  if ( count ($pages) == 1) {
    $xgo = $pages [$file] ['page'] ;
    goto go;
  }

?>