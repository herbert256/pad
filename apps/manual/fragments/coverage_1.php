<?php

  $template = "{if \$stock gt 0}In stock{else}Sold out{/if}";
  $items    = padCoverageMark ( padCoverageItems ( $template ), [], [ 'if $stock gt 0' => [ 'if' => 12 ] ] );
  $marked   = padCoverageHtml ( $template, $items );

?>
