<?php

  // Winds a sequence run down, called from sequence/tag.php once the terms have been built
  // into $pqResult. Runs the stages in order: the sequence type's own exit hook, the
  // {action} transformations, the named store (push/pull), the toData data store, the shape
  // of the rows handed to the template, the extra columns describing how each term came
  // about, the parameters consumed, and the debug info block.
  //
  // Net effect: $pqResult becomes $padData[$pad], the rows the tag iterates over.

  include PQ . 'exits/exit.php';
  include PQ . 'exits/actions.php';
  include PQ . 'exits/store/store.php';
  include PQ . 'exits/data.php';
  include PQ . 'exits/return/return.php';
  include PQ . 'exits/extra/extra.php';
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

?>