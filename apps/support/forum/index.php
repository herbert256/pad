<?php

  $title = 'Forum';

  // Get boards with topic/post counts
  $boards = db("ARRAY
    b.*,
    (SELECT COUNT(*) FROM forum_topics WHERE board_id = b.id) as topic_count,
    (SELECT COUNT(*) FROM forum_posts p JOIN forum_topics t ON p.topic_id = t.id WHERE t.board_id = b.id) as post_count
    FROM forum_boards b
    ORDER BY b.sort_order
  ");

?>
