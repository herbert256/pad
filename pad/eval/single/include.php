<?php

  // include: - returns an _include/ snippet, the expression form of what types/include.php
  // serves as a tag. get/include.php finds it along the directory chain and hands back its
  // source, its .php half echoed and its .pad half appended, exactly as the tag gets it.
  //
  // The tag form is then rendered by the level machinery, which an expression has no part in,
  // so the source is run through padCode() here. Without that the tags inside a snippet would
  // come back written out instead of expanded, which is not what {include:name} means anywhere
  // else. It used to return the literal string 'todo'.

  $padIncludeSource = include PAD . 'get/include.php';

  return padCode ( $padIncludeSource );

?>