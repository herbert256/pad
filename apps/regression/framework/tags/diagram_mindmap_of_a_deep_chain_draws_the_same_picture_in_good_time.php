<?php

  // A mind map that is one chain of 2000 nested lines, each indented one more than the last.

  $chain = "{diagram:mindmap}\n";

  for ( $i = 0; $i < 2000; $i++ )
    $chain .= str_repeat ( ' ', $i ) . "n$i\n";

  $chain .= "{/diagram:mindmap}";

?>
