<?php

  // No cases. The ten tableFun pages need the {content 'cell'} definition their _lib
  // carries, and a sandbox case has no _lib - rendered here, {cell} cannot resolve and the
  // literal tag would leak into the answer. The pages are asserted properly as pages, in
  // regression3/tableFun.

  return [];

?>
