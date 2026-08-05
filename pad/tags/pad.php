<?php

  // The {pad} tag: a level with no behaviour of its own that returns TRUE, so its content
  // always renders once. It exists purely as a carrier for the generic level machinery -
  // data=, content=, name=, level and occurrence variables, pipes, properties - on a tag
  // that adds nothing else, and as what the pad: prefix resolves to when no tag is named.

  return TRUE;

?>