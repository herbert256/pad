<?php

  $padSelect ['users']        = [ 'key' => 'id' ];
  $padSelect ['forum_boards'] = [ 'key' => 'id' ];
  $padSelect ['forum_topics'] = [ 'key' => 'id' ];
  $padSelect ['forum_posts']  = [ 'key' => 'id' ];
  $padSelect ['news']         = [ 'key' => 'id' ];
  $padSelect ['tickets']      = [ 'key' => 'id' ];

  $padRelations ['forum_topics']    ['forum_boards']  = [ 'key' => 'board_id'  ];
  $padRelations ['forum_topics']    ['users']         = [ 'key' => 'user_id'   ];
  $padRelations ['forum_posts']     ['forum_topics']  = [ 'key' => 'topic_id'  ];
  $padRelations ['forum_posts']     ['users']         = [ 'key' => 'user_id'   ];
  $padRelations ['news']            ['users']         = [ 'key' => 'user_id'   ];
  $padRelations ['tickets']         ['users']         = [ 'key' => 'user_id'   ];
  $padRelations ['ticket_comments'] ['tickets']       = [ 'key' => 'ticket_id' ];
  $padRelations ['ticket_comments'] ['users']         = [ 'key' => 'user_id'   ];

  $padSelect ['openBugs'] = [ 'base'  => "tickets", 
                              'where' => "`type`='bug' and `status`='open'",
                              'order' => "updated_at desc"
                            ];

?>