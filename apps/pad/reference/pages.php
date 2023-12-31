<?php

  if ( ! isset ( $pages ) ) $pages = 'tag/pad/pad.txt';
  if ( ! isset ( $for   ) ) $for   = 'tags';
  if ( ! isset ( $base  ) ) $base  = '';
  if ( ! isset ( $item  ) ) $item  = 'pad';

  $pages = array_unique ( file ( padApp . "_xref/manual/$pages", FILE_IGNORE_NEW_LINES ) );

  if ( count ($pages) == 1 )
    padRedirect ( 'reference/go',
                  [ 'go'    => $pages [0],
                    'item'  => $item,
                    'for'   => $for ] );

  $title = "$for - $item";


?>