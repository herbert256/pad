<?php

  return db ("RECORD * FROM forum_topics WHERE id={0}", [$id] );

?>
