<?php

  // Pipe function slug(separator): a readable URL part - 'Crème Brûlée & Co.' becomes
  // 'creme-brulee-co', '-' unless another separator is given. The work is padStrSlug in
  // lib/str.php, which a page's PHP calls too: a slug kept in a database column is then the
  // one a template makes for the link.

  return padStrSlug ( $value, $parm [0] ?? '-' );

?>
