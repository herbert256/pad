<?php

  padDumpToFile ( "track/$padPage/" . $GLOBALS ['padReqID'] . '_' . uniqid() . ".html", padContent () );

  return NULL;
  
?>