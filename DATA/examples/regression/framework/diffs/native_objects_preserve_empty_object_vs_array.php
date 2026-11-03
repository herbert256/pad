<?php
  $before = new stdClass;
  $after = [];
  $html = padUnprotect ( padUnescape ( padCode ( '{diff:json $before, $after/}' ) ) );
  $answer = str_contains ( $html, 'pad-diff-changed' ) && str_contains ( $html, '<code>{}</code>' )
    && str_contains ( $html, '<code>[]</code>' ) ? 'native empty objects and arrays remain different' : 'FAIL';
?>
