<?php

  // Every statement db() sends, kept for the pages here to show what a select asked.

  $GLOBALS ['caseSql'] [] = preg_replace ( '/\s+/', ' ', $sql );

?>
