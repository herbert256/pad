<?php

  $html = padUnprotect ( padUnescape ( padCode ( "{widget:skeleton shape='table', lines=2, columns=4, label='<Loading>'/}" ) ) );
  $answer = substr_count ( $html, 'class="sk-line"' ) === 8 && str_contains ( $html, 'aria-label="&lt;Loading&gt;"' )
         && str_contains ( $html, 'aria-busy="true"' ) && str_contains ( $html, 'aria-hidden="true"' )
         ? 'eight table cells with a single accessible loading label' : 'FAIL';

?>
