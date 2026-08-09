<?php

  // The regression tests whose source names the item - the asserted counterpart of the
  // pages view, which shows real usage.

  if ( ! isset ( $type ) ) $type = 'PAD Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag/pad';
  if ( ! isset ( $item ) ) $item = 'switch';

  $caseRows   = getReferenceCaseList ( $item, $xref );
  $casesCount = count ( $caseRows );

  $title = "Reference - $type - $item - test cases";

?>