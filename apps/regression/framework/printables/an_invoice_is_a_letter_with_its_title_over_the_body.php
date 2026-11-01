<?php

  // An invoice is the letter's page with title= over its body - Invoice unless told - its
  // <address> in the field of the window, the rest the body under the title.

  $answer = printablesShow ( <<<'PAD'
{printable:invoice title='Credit note'}<address>Ann Smith</address><table><tr><th>Item</th><th>Amount</th></tr><tr><td>Labels</td><td>24.00</td></tr></table>{/printable:invoice}
PAD, 12 );

?>
