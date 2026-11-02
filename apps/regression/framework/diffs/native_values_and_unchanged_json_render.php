<?php
  $before = [ 'name' => 'Ada', 'count' => 1 ];
  $after = [ 'count' => 1, 'name' => 'Ada' ];
  $html = padUnprotect ( padUnescape ( padCode ( '{diff:json $before, $after/}' ) ) );
  $answer = str_contains ( $html, 'No changes.' ) && ! padTagBuiltIn ( 'diff' ) && padKinds ( 'diffs' ) === [ 'json', 'lines', 'rows', 'side', 'words' ]
    ? 'native values compare by structure and five kinds replace the old tag' : 'FAIL';
?>
