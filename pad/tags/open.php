<?php

  // The {open} tag: yields &open;, PAD's escaped form of {, which exits/exits.php turns back
  // into a literal brace. Lets a template print { without the parser reading it as a tag.

  return '&open;';

?>