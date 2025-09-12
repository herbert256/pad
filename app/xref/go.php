<?php

  if ( ! isset ( $go   ) ) $go   = 'tags/if_1';
  if ( ! isset ( $type ) ) $type = 'Tags';
  if ( ! isset ( $item ) ) $item = 'if';

  $source   = padFileGet ( APP . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

  $title .= " - $type - $item";

?>