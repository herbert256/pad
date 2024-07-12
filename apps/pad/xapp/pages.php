<?php

  if ( ! isset ( $pages ) ) $pages = 'tag/pad/if.txt';
  if ( ! isset ( $item  ) ) $item  = 'if';
  if ( ! isset ( $type  ) ) $type  = 'Tags';

  $pages = file ( padApp . "_xapp/$pages", FILE_IGNORE_NEW_LINES );

  if ( count ($pages) == 1 )
    padRedirect ( 'xapp/go',
                  [ 'pages' => $pages [0],
                    'type'  => $type ?? '',
                    'item'  => $item ?? ''] );

  $title .= " - $type - $item";

?>