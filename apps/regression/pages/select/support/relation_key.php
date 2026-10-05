<?php

  // A relation in the form DATABASE.md gives - [ 'key' => 'user_id' ] - names the field of
  // the first table that holds the declared key of the second. It was read as a field named
  // key, and the nested selects ended the request on an undefined array key.

  $padRelations = [];

  $padRelations ['forum_topics'] ['users']        = [ 'key' => 'user_id'  ];
  $padRelations ['forum_posts']  ['forum_topics'] = [ 'key' => 'topic_id' ];
  $padRelations ['forum_posts']  ['users']        = [ 'key' => 'user_id'  ];

?>
