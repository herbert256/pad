<?php

  if ( ! isset ( $xref  ) ) $xref  = 'manual';
  if ( ! isset ( $pages ) ) $pages = 'tag/pad/if.txt';
  if ( ! isset ( $base  ) ) $base  = '';
  if ( ! isset ( $item  ) ) $item  = '';
  if ( ! isset ( $for   ) ) $for   = '';

  $pages = file ( padApp . "_xref/$xref/$pages", FILE_IGNORE_NEW_LINES );

  if ( count ($pages) == 1 )
    padRedirect ( 'xref/go',
                  [ 'go'    => $pages [0],
                    'for'   => $for ?? '',
                    'item'  => $item ?? '',
                    'base'  => $base ?? '' ] );

                       $title  = 'Reference';
  if ( isset ($for)  ) $title .= " - $for";
  if ( isset ($base) ) $title .= " - $base";
  if ( isset ($item) ) $title .= " - $item";

?>