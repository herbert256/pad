<?php

  if ( ! isset ( $go    ) ) $go    = 'hello/hello';
  if ( ! isset ( $for   ) ) $for   = 'tags';
  if ( ! isset ( $base  ) ) $base  = '';
  if ( ! isset ( $item  ) ) $item  = 'pad';

  $title    = "$for - $item - $go";
  $source   = padFileGetContents ( padApp . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

?>