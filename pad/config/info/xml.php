<?php

  // $padInfo selector 'xml': enables the xml info type (info/types/xml/), which mirrors the
  // whole level/occurrence tree of the request as an XML document, DATA/_xml/complete/
  // <page>.xml (include/ for an included page). Parms gives a level with more than one
  // option, parameter or variable a <parms> block of them, and Tidy re-indents the file
  // with PHP's tidy extension. Compact, the bare skeleton written to DATA/_xml/compact/ in
  // place of the full report - no attributes, occurrences or <parms> - is left off, so
  // Parms shows in the report; config/info/all.php explains each flag.

  $padInfoXml          = TRUE;

  $padInfoXmlParms     = TRUE;
  $padInfoXmlCompact   = FALSE;
  $padInfoXmlTidy      = TRUE;

?>
