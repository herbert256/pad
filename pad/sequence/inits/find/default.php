<?php

  // Fallback when nothing named a sequence type or a store: plain 'loop', which just counts
  // from $pqFrom, so a bare {sequence rows=5} still produces something.

  $pqSeq  = 'loop';
  $pqParm = '';

?>