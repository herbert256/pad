<?php

  // A numeric reference to a control character inside the scheme: html_entity_decode leaves
  // &#13; &#x0D; &#1; &#11; as they are, a browser decodes them and drops the control
  // character, so java&#13;script: was javascript:. A link with a reference to an ordinary
  // character keeps it.

  $text = '[a](java&#13;script:alert(1)) [b](&#1;javascript:alert(1)) [c](java&#x0D;script:alert(1)) [d](java&#11;script:x) [ok](https://e.com/?a=1&#38;b=2)';

?>
