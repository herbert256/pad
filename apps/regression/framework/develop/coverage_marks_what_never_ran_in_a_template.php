<?php

  $coverSource = "{if \$a eq 1}x{else}y{/if}{items}{\$name}{/items}";
  $coverItems  = padCoverageMark ( padCoverageItems ( $coverSource ), [ 'items' => 0 ], [ 'if $a eq 1' => [ 'else' => 2 ] ] );
  $coverHtml   = padCoverageHtml ( $coverSource, $coverItems );

?>
