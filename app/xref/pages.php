<?php

  if ( ! isset ( $pages ) ) $pages = 'tag/pad/if.txt';
  if ( ! isset ( $type  ) ) $type  = 'Tags';
  if ( ! isset ( $item  ) ) $item  = 'if';

  $go = file ( DATA . "_xref/$pages", FILE_IGNORE_NEW_LINES );

  $title .= " - $type - $item";

?>