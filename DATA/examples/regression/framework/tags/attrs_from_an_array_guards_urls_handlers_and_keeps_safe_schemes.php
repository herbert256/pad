<?php

  // The keys and values of an array are data. A URL attribute is judged as it will be
  // written, an array value joined too, so href=['javascript:...'] is left out; the event
  // handlers of HTML and of the JS libraries - on..., @click, x-on:, hx-on:, :bind - and an
  // iframe srcdoc are never written from an array; and a safe scheme a visitor may send -
  // a data:image/ source, an sms: link - is kept, where the Markdown allowlist dropped it.

  $bad = [ 'href' => [ 'javascript:alert(1)' ], 'onclick' => 'x', '@click' => 'y',
           'x-on:click' => 'z', 'hx-on:click' => 'w', ':id' => 'q', 'srcdoc' => '<b>',
           'title' => 'ok' ];
  $ok  = [ 'src' => 'data:image/png;base64,iVBORw0KGgo=', 'href' => 'sms:+31612345678' ];
  $no  = [ 'src' => 'data:text/html,<script>1</script>' ];

?>
