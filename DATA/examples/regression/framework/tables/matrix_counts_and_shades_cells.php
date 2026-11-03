<?php
  $html = padUnprotect ( padUnescape ( padCode ( "{table:matrix row='r', column='c'}[{\"r\":\"A\",\"c\":\"X\"},{\"r\":\"A\",\"c\":\"X\"},{\"r\":\"B\",\"c\":\"Y\"}]{/table:matrix}" ) ) );
  $answer = str_contains ( $html, '<td class="num pad-table-heat-5">2</td>' )
    && str_contains ( $html, '<td class="num pad-table-heat-1">0</td>' ) && str_contains ( $html, '<td class="num pad-table-heat-3">1</td>' ) ? 'matrix defaults to count and shades the full range' : 'FAIL';
?>
