<?php
  $rows = [ [ 'name' => 'Zoë', 'note' => "  a|b\n\"quoted\"  ", 'missing' => '' ], [ 'name' => '李', 'note' => 'C:\\tmp' ] ];
  $written = padFormatWrite ( 'psv', $rows );
  $read = padFormatRead ( 'psv', $written );
  $answer = $read === [ $rows [0], $rows [1] + [ 'missing' => '' ] ]
    && str_contains ( $written, '""quoted""' ) && padFormatFind ( 'psv', 'read' ) === 'psv'
    ? 'PSV quotes pipes and newlines, preserves Unicode and fills missing cells' : 'FAIL';
?>
