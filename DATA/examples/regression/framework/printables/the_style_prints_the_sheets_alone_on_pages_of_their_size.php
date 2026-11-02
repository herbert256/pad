<?php

  // The style of a printable: every sheet the size of its paper in millimetres and on a named
  // page of that size without a margin - @page pp-<id> - its places classes of the style, no
  // inline style anywhere, scale= a zoom of the screen alone; and the style every printable
  // shares prints the sheets alone, everything round them out of the way.

  $out = padUnprotect ( padUnescape ( padCode ( "{printable:envelope scale=0.5}Ann{/printable:envelope}" ) ) );

  preg_match ( '/\.(pad-printable-[0-9a-f]{8}) \.pp-sheet\{width:([^;]+);height:([^;]+);page:(pp-[0-9a-f]{8})\}@page \4\{size:([^}]+)\}/', $out, $m );

  $answer = ( $m ? "sheet {$m [2]} by {$m [3]} on page " . ( $m [4] ? 'pp-id' : '?' ) . ", @page size:{$m [5]}" : 'no sheet rule' )
          . ( str_contains ( $out, ' style="' ) ? ', inline styles' : ', no inline style' )
          . ( preg_match ( '/@media screen\{\.pad-printable-[0-9a-f]{8} \.pp-sheets\{zoom:0\.5\}\}/', $out ) ? ', zoom 0.5 on the screen' : '' )
          . ( str_contains ( $out, 'body :not(:has(.pad-printable)):not(.pad-printable):not(.pad-printable *){display:none!important}' ) ? ', printed alone' : '' );

?>
