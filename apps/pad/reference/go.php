<?php

  if ( ! isset ( $go )   ) $go   = 'tags/if_1';
  if ( ! isset ( $item ) ) $item = '';

  $source   = padFileGetContents ( padApp . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

?>