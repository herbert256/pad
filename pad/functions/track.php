<?php

  padDumpToFile ("track/$app/$page/" . $GLOBALS ['PADREQID'] . '_' . uniqid() . ".html", $padPrm [$pad]);

  return NULL;
  
?>