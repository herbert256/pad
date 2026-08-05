<?php

  // Reports whether the run produced anything: TRUE when $padData[$pad] holds rows, which is
  // what a tag handler returns to say it matched. Superseded - sequence/sequence/tag.php now
  // makes that decision inline, so nothing includes this file any more.

  if   ( count ( $padData [$pad] ) ) return TRUE;
  else                               return FALSE;

?>