<?php
  // Structured change records. Existing word/line renderers remain compatibility APIs.
  function padDiffCompare ( $kind, $before, $after, $options = [] ) {
    include_once PAD . 'subs/diffs/_lib/compare.php';
    if ( ! padKind ( 'diffs', $kind ) ) throw new InvalidArgumentException ( 'Unknown diff kind' );
    $type = include PAD . "subs/diffs/types/$kind.php";
    return $type ['compare'] ( $before, $after, $options );
  }

  function padDiffRender ( $changes, $side = FALSE, $from = 'Before', $to = 'After' ) {
    include_once PAD . 'subs/diffs/_lib/compare.php';
    return padDiffStructuredHtml ( $changes, $side, $from, $to );
  }
?>
