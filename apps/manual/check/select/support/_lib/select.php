<?php

  $padSelect ['users']        = [ 'key' => 'id' ];
  $padSelect ['forum_boards'] = [ 'key' => 'id' ];
  $padSelect ['forum_topics'] = [ 'key' => 'id' ];
  $padSelect ['forum_posts']  = [ 'key' => 'id' ];
  $padSelect ['news']         = [ 'key' => 'id' ];
  $padSelect ['tickets']      = [ 'key' => 'id' ];

  $padRelations ['forum_topics']    ['forum_boards']  = [ 'board_id'  => 'id' ];
  $padRelations ['forum_topics']    ['users']         = [ 'user_id'   => 'id' ];
  $padRelations ['forum_posts']     ['forum_topics']  = [ 'topic_id'  => 'id' ];
  $padRelations ['forum_posts']     ['users']         = [ 'user_id'   => 'id' ];
  $padRelations ['news']            ['users']         = [ 'user_id'   => 'id' ];
  $padRelations ['tickets']         ['users']         = [ 'user_id'   => 'id' ];
  $padRelations ['ticket_comments'] ['tickets']       = [ 'ticket_id' => 'id' ];
  $padRelations ['ticket_comments'] ['users']         = [ 'user_id'   => 'id' ];

  $padSelect ['openBugs'] = [ 'base'  => "tickets",
                              'where' => "`type`='bug' and `status`='open'",
                              'order' => "updated_at desc"
                            ];

?>