<?php
  $answer = padKinds ( 'tables' ) === [ 'comparison', 'grouped', 'masterdetail', 'matrix', 'pivot', 'ranked', 'standard', 'tree' ] && ! padTagBuiltIn ( 'datatable' )
    && ! is_file ( PAD . 'tags/_lib/datatable.php' ) ? 'eight table kinds replace the old tag and helper' : 'FAIL';
?>
