<?php

  if ( ! isset ( $go     ) ) $go     = 'hello/hello';
  if ( ! isset ( $first  ) ) $first  = '';
  if ( ! isset ( $second ) ) $second = '';
  if ( ! isset ( $xitem  ) ) $xitem  = '';
  if ( ! isset ( $for    ) ) $for    = '';

                 $title  = $for;
  if ( $xitem  ) $title .= " - $xitem";
  if ( $second ) $title .= " - $second";  
                 $title .= " - $go";

  $file = padApp . $go . '.pad';

  $source   = padFileGetContents ( padApp . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

?>