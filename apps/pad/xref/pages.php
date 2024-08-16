<?php

  if ( ! isset ( $pages ) ) $pages = 'tag/pad/if.txt';
  if ( ! isset ( $type  ) ) $type  = 'Tags';
  if ( ! isset ( $item  ) ) $item  = 'if';

  $go = file ( "/app/_xref/$pages", FILE_IGNORE_NEW_LINES );

  if ( count ($go) == 1 )
    padRedirect ( 'xref/go',
                  [ 'go'   => $go [0],
                    'type' => $type ?? '',
                    'item' => $item ?? ''] );

  $title .= " - $type - $item";

?>