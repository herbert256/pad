<?php

  // Every list item of a {pdf} has its marker: one that starts with a table, one that is
  // empty and one that starts with a list of its own got none - "5." and "6." were missing,
  // and the inner bullet took the outer one's place - and start="0" counts from 0, as HTML.

  $pdf = padPdf ( '<ol start="3"><li>three<ol><li>inner a</li></ol></li><li><p>para</p></li><li><table><tr><td>table in li</td></tr></table></li>'
                . '<li></li><li>last</li></ol><ol start="0"><li>zero</li></ol><ul><li><ul><li>nested first</li></ul></li></ul>', [ 'compress' => FALSE ] );

  preg_match_all ( '#\(([^)]*)\) Tj#', $pdf, $found );

  $texts = implode ( ' ', array_map ( fn ( $text ) => $text === "\x95" ? '*' : $text, $found [1] ) );

?>
