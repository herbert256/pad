<?php

  $file = padApp . $item;

  if ( file_exists ( padApp . $item ) )
    return padColorsFile ( padApp . $item );
  else
    return NULL;

  exit;

?>