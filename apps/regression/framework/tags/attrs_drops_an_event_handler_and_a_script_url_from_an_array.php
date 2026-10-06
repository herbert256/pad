<?php

  // The keys of an array are data - often a visitor's, decoded from JSON - so an event
  // handler among them, or a URL attribute with a scheme that runs script, is left out.

  $extra = [ 'onmouseover' => 'alert(1)', 'ONclick' => 'x', 'href' => 'java&#13;script:alert(2)',
             'title' => 'ok', 'src' => 'https://x/a.png', 'action' => 'javascript:y' ];

?>
