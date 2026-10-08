<?php

  // The state of the card: an array of the page's PHP, written into x-data as JSON by the ^
  // sigil - escaped for the attribute, so a quote or a brace in a value stays text. Alpine
  // reads it as the object it is and keeps it live; nothing goes back to the server.

  $profile = [ 'name'   => 'Ada Fernández',
               'role'   => 'Lead engineer',
               'city'   => 'Amsterdam',
               'bio'    => 'Builds things that render on the server and wake up in the browser.',
               'likes'  => 12,
               'liked'  => FALSE,
               'skills' => [ 'PHP', 'PAD', 'Alpine', 'SQL' ],
               'skill'  => '' ];

?>
