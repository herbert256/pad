<?php

  // The download writer's promise: the page arrives as a file attachment - a
  // Content-Disposition naming a file, the body intact behind it.

  $r = padCurl ( $padHost . 'regression/output_download/?payload&padInclude' );

  $disposition = $r ['headers'] ['Content-Disposition'] ?? '';

  $verdict = ( str_starts_with ( $r ['result'], '2' )
               and str_contains ( $disposition, 'attachment' )
               and str_contains ( $r ['data'], 'CARRIED ALL THE WAY' ) ) ? 'yes' : 'NO';

  $output = 'download';

?>