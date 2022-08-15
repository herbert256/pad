<?php

  pDump_to_file ("track/$app/$page/" . $GLOBALS ['PADREQID'] . '_' . uniqid() . ".html", $padPrm [$pad]);

  return NULL;
  
?>