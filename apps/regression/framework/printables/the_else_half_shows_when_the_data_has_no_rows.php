<?php

  // A printable whose data has no rows renders its @else@ half, which no item marker cuts:
  // neither sheets nor a bar - the text of the half as it is.

  $answer = trim ( padUnprotect ( padUnescape ( padCode ( <<<'PAD'
{format:auto 'nobody'}[]{/format:auto}
{printable:labels data='nobody'}{$name}@else@No labels to print.{/printable:labels}
PAD ) ) ) );

?>
