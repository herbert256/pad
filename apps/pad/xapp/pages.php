<?php

  if ( ! isset ( $pages ) ) $pages = 'tag/pad/if.txt';
  if ( ! isset ( $base  ) ) $base  = '';
  if ( ! isset ( $item  ) ) $item  = '';
  if ( ! isset ( $for   ) ) $for   = '';

  $pages = file ( padApp . "_xapp/$pages", FILE_IGNORE_NEW_LINES );

  if ( count ($pages) == 1 )
    padRedirect ( '_xapp/go',
                  [ 'go'    => $pages [0],
                    'for'   => $for ?? '',
                    'item'  => $item ?? '',
                    'base'  => $base ?? '' ] );

               $title  = 'Reference';
  if ( $for  ) $title .= " - $for";
  if ( $base ) $title .= " - $base";
  if ( $item ) $title .= " - $item";

?>