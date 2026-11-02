<?php

  $coverSource = "{pad:if \$a eq 1}x{pad:else}y{/pad:if}{array:items}{\$name}{/array:items}";
  $coverItems  = padCoverageMark ( padCoverageItems ( $coverSource ), [ 'array:items' => 0 ], [ 'pad:if $a eq 1' => [ 'else' => 2 ] ] );
  $coverHtml   = padCoverageHtml ( $coverSource, $coverItems );

?>
