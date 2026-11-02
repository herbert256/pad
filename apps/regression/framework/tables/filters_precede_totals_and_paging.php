<?php
  $_GET = [ 'sale_filter' => [ 'region' => 'wes', 'amount' => '999', 'unknown' => 'no' ], 'sale_page' => '1' ];
  $html = padUnprotect ( padUnescape ( padCode ( "{table:standard filters='region', query='sale_', totals='amount', rows=1}[{\"region\":\"West\",\"amount\":10},{\"region\":\"East\",\"amount\":20},{\"region\":\"WEST\",\"amount\":5}]{/table:standard}" ) ) );
  $answer = ! str_contains ( $html, '<td>East</td>' ) && ! str_contains ( $html, '<td>WEST</td>' )
    && str_contains ( $html, '<td class="num">15</td>' ) && str_contains ( $html, 'sale_filter[region]' )
    && str_contains ( $html, 'sale_page=2' ) ? 'only allowed filters apply before totals and paging' : 'FAIL';
?>
