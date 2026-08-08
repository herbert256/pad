<?php

  // The sandbox cases whose template names the item, from the last run of each group - the
  // asserted counterpart of the pages view, which shows real usage.

  if ( ! isset ( $type ) ) $type = 'PAD Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'switch';

  $caseRows   = getReferenceCaseList ( $item, $xref );
  $casesCount = count ( $caseRows );

  $title = "Reference - $type - $item - sandbox cases";

?>
