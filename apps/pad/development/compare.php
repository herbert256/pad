<?php

  $showTitle = FALSE;

  $htmlOld = padApp . "_regression/$item.html";
  $htmlNew = padApp . "$item.html";

  $old = padFileGetContents ( $htmlOld );

  $new = getPage ($item);
  $new = $new ['data'];

  $onlyResult = onlyResult ($htmlNew);

  $extraDir = substr($item, 0, strrpos($item, '/') );

?>