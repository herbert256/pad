<?php

  $padSeqEntryType = 'tag';

  include '/pad/sequence/entry/_lib/inits.php';
  
  return include "/pad/sequence/entry/tags/$padTag[$pad].php";

?>