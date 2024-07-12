<?php

  $source   = padFileGetContents ( padApp . $go . '.pad' );
  $showPage = ( str_contains ( $source, '{demo}' ) or str_contains ( $source, '{example' )  );

  $title .= " - $type - $item";

?>