<?php

  $item = $item ?? 'examples/hello';

  $old = padFileGetContents ( padApp . "regression/$item.html" );
  $new = padInclude ($item);
 
?>