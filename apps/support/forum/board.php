<?php

  if (!db("CHECK forum_boards WHERE slug = '{0}'", [$slug]))
    padRedirect('forum/index');

  $title = db("FIELD name FROM forum_boards WHERE slug = '{0}'", [$slug]);

?>
