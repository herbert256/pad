<?php

  $source = "{widget:progressring 99.6, label='<Upload>'/}{widget:progressring 120, max=100/}";
  $html = padUnprotect ( padUnescape ( padCode ( $source ) ) );
  $answer = str_contains ( $html, 'aria-label="&lt;Upload&gt;"' ) && str_contains ( $html, '>99%</text>' )
         && str_contains ( $html, '>100%</text>' ) && str_contains ( $html, 'aria-valuenow="100"' )
         && substr_count ( $html, 'role="progressbar"' ) === 2 ? 'rings clamp and report completion accurately' : 'FAIL';

?>
