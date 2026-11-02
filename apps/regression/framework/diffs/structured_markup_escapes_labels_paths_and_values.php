<?php
  $html = padUnprotect ( padUnescape ( padCode ( "{diff:json '{\"<key>\":null}', '{\"<key>\":\"<script>\",\"added\":null}', from='<old>', to='<new>'/}" ) ) );
  $answer = str_contains ( $html, '&lt;old&gt;' ) && str_contains ( $html, '/&lt;key&gt;' )
    && str_contains ( $html, '&lt;script&gt;' ) && ! str_contains ( $html, '<script>' )
    && str_contains ( $html, 'pad-diff-absent">Absent' ) && str_contains ( $html, '<code>null</code>' ) ? 'structured markup escapes all values and distinguishes absent from null' : 'FAIL';
?>
