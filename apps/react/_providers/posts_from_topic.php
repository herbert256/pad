<?php

  return array_values ( 
    db ( "ARRAY p.*, u.username
               FROM forum_posts p
               JOIN users u ON p.user_id = u.id
               WHERE p.topic_id={0}
               ORDER BY p.created_at", [ $padProviders ['topic'] ['id'] ] )
    );

?>