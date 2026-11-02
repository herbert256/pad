<?php

  // A value written raw into an item - {pad:echo $v/} - that holds the marker a pair puts in
  // front of every item is one item still: the marker is sealed (padMarkSeal), and no value
  // carries the seal. With <!--pad:printable-item--> for every printable, a visitor's value
  // that held it made two labels of one, a ticket more and its number.

  $v = 'x<!--pad:printable-item-->y<!--0000000000000000-->z';

  $answer = printablesShow ( "{printable:labels}A{pad:echo \$v/}B{/printable:labels}", 2 );

?>
