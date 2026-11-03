<?php
  $source = "{table:standard filters='name'}[{\"name\":\"Ada\"}]{/table:standard}";
  $ok = TRUE;
  foreach ( [ 'not-an-array', [ 'name' => [ 'nested' ] ] ] as $filter ) {
    $_GET = [ 'filter' => $filter ];
    $html = padUnprotect ( padUnescape ( padCode ( $source ) ) );
    $ok = $ok && str_contains ( $html, '<td>Ada</td>' );
  }
  $_GET = [ 'filter' => [ 'name' => '<missing>' ] ];
  $html = padUnprotect ( padUnescape ( padCode ( $source ) ) );
  $answer = $ok && str_contains ( $html, 'No matching rows.' ) && str_contains ( $html, '&lt;missing&gt;' ) && str_contains ( $html, '<form' ) ? 'malformed filters ignored and empty results remain editable' : 'FAIL';
?>
