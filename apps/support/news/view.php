<?php

  if (!db("CHECK news WHERE id = {0}", [$id]))
    padRedirect('news/index');

  $title = db("FIELD title FROM news WHERE id = {0}", [$id]);

?>