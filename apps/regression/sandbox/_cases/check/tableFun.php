<?php

  // No cases. The ten tableFun pages need the {content 'cell'} definition their _lib
  // carries, and a sandbox case has no _lib - rendered here, {cell} leaked its own source
  // into the answer, and nine expectations had recorded that leak as if it were the table.
  // The pages are asserted properly in regression3/tableFun, where the audit that caught
  // this found all ten sound.

  return [];

?>
