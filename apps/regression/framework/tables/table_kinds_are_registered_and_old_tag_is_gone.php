<?php
  $answer = padKinds ( 'tables' ) === [ 'grouped', 'matrix', 'pivot', 'standard' ] && ! padTagBuiltIn ( 'datatable' )
    && ! is_file ( PAD . 'tags/_lib/datatable.php' ) ? 'four table kinds replace the old tag and helper' : 'FAIL';
?>
