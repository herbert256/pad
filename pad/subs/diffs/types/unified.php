<?php
  // unified - Context hunks with old/new line ranges and explicit final-newline markers.
  include_once PAD.'subs/diffs/_lib/unified.php';
  return ['compare'=>fn($a,$b,$options)=>padDiffTextRecords($a,$b,TRUE),
    'render'=>fn($a,$b,$options)=>padDiffUnified($a,$b,$options)];
?>
