<?php

  $old = padFileGetContents ( padApp . "_regression/$item.html" );
  $new = getPage ($item);
  $new = $new ['data'];

?>