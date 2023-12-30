<?php

  if ( ! isset ( $go     ) ) $go     = 'hello/hello';
  if ( ! isset ( $xitem  ) ) $xitem  = '';
  if ( ! isset ( $for    ) ) $for    = '';

                 $title  = $for;
  if ( $xitem  ) $title .= " - $xitem";
                 $title .= " - $go";

  $file = padApp . $go . '.pad';

  $source   = padFileGetContents ( padApp . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

?>