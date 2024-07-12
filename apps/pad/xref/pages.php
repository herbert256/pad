<?php

  $go = file ( padApp . "_xref/$pages", FILE_IGNORE_NEW_LINES );

  if ( count ($go) == 1 )
    padRedirect ( 'xref/go',
                  [ 'go'   => $go [0],
                    'type' => $type ?? '',
                    'item' => $item ?? ''] );

  $title .= " - $type - $item";

?>