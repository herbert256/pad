<?php

  // The data of the components: plain arrays here, a database or a _data file anywhere else.
  // The template hands each to its component as the argument - x-data="accordion({^faq})" -
  // and renders the same rows as HTML inside it, for a visitor without JavaScript.

  $faq = [ [ 'q' => 'Why not write the state in the template?',
             'a' => 'Alpine writes objects with braces, and PAD reads every brace of a template as the start of a tag. Let PAD write the JSON instead.' ],
           [ 'q' => 'Where do the components live?',
             'a' => 'In www/alpine/examples/components.js - a file the web server sends as it is, so its braces never meet the template parser.' ],
           [ 'q' => 'Does it work without JavaScript?',
             'a' => 'The answers are in the HTML PAD wrote; x-cloak and x-show only fold them away once Alpine runs.' ] ];

  $tabs = [ [ 'key' => 'server', 'label' => 'Server', 'text' => 'PAD renders the page, with every value escaped for where it stands.' ],
            [ 'key' => 'wire',   'label' => 'Wire',   'text' => 'The state crosses as JSON in an attribute - no second request.' ],
            [ 'key' => 'client', 'label' => 'Client', 'text' => 'Alpine reads it and keeps the component live in the browser.' ] ];

?>
