<?php
  $answer = padKinds ( 'tables' ) === [ 'comparison', 'grouped', 'matrix', 'pivot', 'standard', 'tree' ] && ! padTagBuiltIn ( 'datatable' )
    && ! is_file ( PAD . 'tags/_lib/datatable.php' ) ? 'six table kinds replace the old tag and helper' : 'FAIL';
?>
