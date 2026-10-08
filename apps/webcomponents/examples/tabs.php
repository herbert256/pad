<?php

  // The tabs and their panels: a button with slot="tab" and a section with slot="panel" each,
  // the light DOM of <pad-tabs>. Before tabs.js has defined the element, the page's CSS shows
  // the first panel only; after, the element switches them.

  $sections = [ [ 'key' => 'server', 'label' => 'On the server',  'text' => 'PAD renders <pad-tabs> with its shadow root - the tablist and the panel area - and every tab and panel as plain HTML inside it.' ],
                [ 'key' => 'wire',   'label' => 'In the HTML',    'text' => 'The browser builds the shadow root from the template while it parses the page: no script, no flash of unstyled tabs.' ],
                [ 'key' => 'client', 'label' => 'In the browser', 'text' => 'tabs.js defines the element: it finds the root already there and adds clicks and the arrow keys.' ] ];

?>
