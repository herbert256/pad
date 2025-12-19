<?php

  $padSelectTables    ['users']            = [ 'key' => 'id' ];
  $padSelectTables    ['forum_boards']     = [ 'key' => 'id' ];
  $padSelectTables    ['forum_topics']     = [ 'key' => 'id' ];
  $padSelectTables    ['forum_posts']      = [ 'key' => 'id', 'order' => 'created_at' ];
  $padSelectTables    ['news']             = [ 'key' => 'id' ];
  $padSelectTables    ['tickets']          = [ 'key' => 'id' ];
  $padSelectTables    ['ticket_comments']  = [ 'key' => 'id' ];

  $padSelectRelations ['forum_topics']    ['forum_boards']  = [ 'key' => 'board_id'  ];
  $padSelectRelations ['forum_topics']    ['users']         = [ 'key' => 'user_id'   ];
  $padSelectRelations ['forum_posts']     ['forum_topics']  = [ 'key' => 'topic_id'  ];
  $padSelectRelations ['forum_posts']     ['users']         = [ 'key' => 'user_id'   ];
  $padSelectRelations ['news']            ['users']         = [ 'key' => 'user_id'   ];
  $padSelectRelations ['tickets']         ['users']         = [ 'key' => 'user_id'   ];
  $padSelectRelations ['ticket_comments'] ['tickets']       = [ 'key' => 'ticket_id' ];
  $padSelectRelations ['ticket_comments'] ['users']         = [ 'key' => 'user_id'   ];

  $padSelectTables    ['openBugs'] = [ 'base'  => "tickets",
                                       'where' => "`type`='bug' and `status`='open'",
                                       'order' => "updated_at desc"
                                     ];

  $padSelectTables    ['pendingTickets'] = [ 'base'  => "tickets",
                                             'where' => "`status` IN ('open', 'in_progress')",
                                             'order' => "CASE `priority` WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END, created_at"
                                           ];

?>