<?php

  // Reports whether the run produced anything: TRUE when $padData[$pad] holds rows, which is
  // what a tag handler returns to say it matched. 

  if   ( count ( $padData [$pad] ) ) return TRUE;
  else                               return FALSE;

?>