<?php

  padDumpToFile ( "track/$padApp/$padPage/" . $GLOBALS ['padReqID'] . '_' . uniqid() . ".html", padContent () );

  return NULL;
  
?>