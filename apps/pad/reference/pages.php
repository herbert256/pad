<?php

  $now   = str_replace ( '.txt', '', substr ( $pages, strrpos($pages, '/') + 1 ) );

  $pages = file ( padApp . "_xref/manual/$pages" );
  $pages = array_unique ($pages);

  if ( ! isset ( $for    ) ) $for    = 'tags';
  if ( ! isset ( $xitem  ) ) $xitem  = 'pad';

                 $title  = $for;
  if ( $xitem  ) $title .= " - $xitem";
                 $title .= " - $now";

  if ( count ($pages) == 1 )
    padRedirect ( $go = 'reference/go',
                  [ 'go'     => $pages [0],
                    'xitem'  => $xitem,
                    'for'    => $for,
                    'now'    => $now ] );
 
?>