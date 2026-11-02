<?php

  $template = "{pad:if \$stock gt 0}In stock{pad:else}Sold out{/pad:if}";
  $items    = padCoverageMark ( padCoverageItems ( $template ), [], [ 'if $stock gt 0' => [ 'if' => 12 ] ] );
  $marked   = padCoverageHtml ( $template, $items );

?>
