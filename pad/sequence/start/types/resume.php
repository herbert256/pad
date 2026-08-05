<?php

  // Type entry for resume, the prefixed form {resume:mySeq}. Currently unreachable: there is
  // no types/resume.php to route here, even though padTypeSeq() will return 'resume' because
  // this file exists. Note also that it runs the ordinary tag pipeline rather than
  // sequence/resume.php, so it would not write back to the store the way {resume} does.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/type.php";

?>