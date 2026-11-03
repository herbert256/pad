<?php
  $html = padUnprotect ( padUnescape ( padCode ( "{table:grouped group='region', totals='amount', rows=2}[{\"region\":\"West\",\"amount\":10},{\"region\":\"East\",\"amount\":20},{\"region\":\"West\",\"amount\":5}]{/table:grouped}" ) ) );
  $answer = substr_count ( $html, 'scope="rowgroup"' ) === 1 && str_contains ( $html, 'West subtotal' )
    && str_contains ( $html, '<td class="num">15</td>' ) && str_contains ( $html, '<td class="num">35</td>' )
    && ! str_contains ( $html, '<td>East</td>' ) ? 'grouped rows, full subtotal and grand total' : 'FAIL';
?>
