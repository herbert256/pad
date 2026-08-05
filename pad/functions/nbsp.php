<?php

  // Pipe function nbsp: turns every space into &nbsp; so the browser neither wraps nor
  // collapses it. Spaces only - tabs and newlines are left alone.

  return str_replace (' ' , '&nbsp;' , $value);

?>