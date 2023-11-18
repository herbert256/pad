<?php

  if ( ! isset ( $xgo   ) ) $xgo   = '';
  if ( ! isset ( $xmain ) ) $xmain = '';
  if ( ! isset ( $xbase ) ) $xbase = '';
  if ( ! isset ( $xitem ) ) $xitem = '';
  if ( ! isset ( $xnext ) ) $xnext = '';

  $title = $for;

  if ( $xitem ) $title .= " - $xitem";
  if ( $xnext ) $title .= " - $xnext";

  if ( $xgo ) {
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

  if ( $xbase and count ( $dirs ) ) {

    $xbase = pad . $xbase;

    foreach ( $dirs as $file => $dir )
      if ( ! file_exists ("$xbase/$file.php") and ! file_exists ("$xbase/$file.pad") and ! file_exists ("$xbase/$file") )
        unset ( $dirs [$file] );

  }

?>