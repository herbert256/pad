<?php
  $html = padUnprotect ( padUnescape ( padCode ( "{table:pivot row='region', column='quarter', value='amount'}[{\"region\":\"001\",\"quarter\":\"Q1\",\"amount\":2}]{/table:pivot}" ) ) );
  $answer = str_contains ( $html, '<td>001</td>' ) && str_contains ( $html, '<td class="num">2</td>' )
    ? 'numeric-looking row categories preserve their exact labels' : 'FAIL';
?>
