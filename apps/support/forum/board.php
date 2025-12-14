<?php

  $slug = $_GET['slug'] ?? '';

  $board = db("RECORD * FROM forum_boards WHERE slug='{0}'", [$slug]);

  if (!$board) {
    padRedirect('forum/index');
  }

  $title = $board['name'];
  $boardId = $board['id'];
  $boardName = $board['name'];
  $boardDescription = $board['description'];

  // Get topics with author and reply count
  $topics = db("ARRAY
    t.*,
    u.username as author_name,
    (SELECT COUNT(*) FROM forum_posts WHERE topic_id = t.id) as reply_count,
    (SELECT MAX(created_at) FROM forum_posts WHERE topic_id = t.id) as last_reply
    FROM forum_topics t
    JOIN users u ON t.user_id = u.id
    WHERE t.board_id = {0}
    ORDER BY t.is_pinned DESC, t.updated_at DESC
  ", [$boardId]);

?>
