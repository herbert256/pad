<?php

  // Works out what the tag is actually asking for: which sequence type, which stored
  // sequence, which action, and which parameter belongs to which of them.
  //
  // A name can arrive in four places - the prefix, the tag name, an option, or the shape of
  // the first parameter - and they are tried in that order, each step only filling what is
  // still empty. The leftover parameter is then placed, first in the unambiguous cases
  // (parm/primary.php) and finally in the awkward ones (parm/parm.php).
  //
  // Two outcomes need fixing up afterwards: nothing matched at all, so find/default.php falls
  // back to 'loop'; or both a store and a type matched, so find/add.php demotes the type to a
  // play applied to the pulled values.
  //
  // Leaves $pqSeq, $pqPull, $pqAction, $pqParm and $pqActionParm for build/ and actions/.

  include PQ . 'inits/find/parm/inits.php';
  include PQ . 'inits/find/prefix.php';
  include PQ . 'inits/find/tag.php';
  include PQ . 'inits/find/parm/quick.php';
  include PQ . 'inits/find/parm/primary.php';
  include PQ . 'inits/find/loop.php';
  include PQ . 'inits/find/pull.php';
  include PQ . 'inits/find/parm/parm.php';

  if     ( ! $pqPull and ! $pqSeq ) include PQ . 'inits/find/default.php';
  elseif (   $pqPull and   $pqSeq ) include PQ . 'inits/find/add.php';

?>