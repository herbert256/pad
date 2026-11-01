<?php

  // The formats are the files of pad/subs/formats/types/ - padFormatKinds reads them there -
  // each saying whether it reads, writes or both; an extension names its format (md is
  // markdown, jsonl ndjson, yml yaml), and a format that cannot do what is asked is none.

  $fmtFiles = array_map ( fn ( $file ) => basename ( $file, '.php' ), glob ( PAD . 'subs/formats/types/*.php' ) );
  $fmtList  = ( padFormatKinds () === $fmtFiles ) ? 'the files of subs/formats/types' : 'another list';

  $fmtCan = [];

  foreach ( padFormatKinds () as $fmtKind )
    $fmtCan [] = $fmtKind . ':' . ( padFormatFind ( $fmtKind, 'read' ) ? 'r' : '' ) . ( padFormatFind ( $fmtKind, 'write' ) ? 'w' : '' );

  $fmtCan  = implode ( ' ', $fmtCan );
  $fmtExts = implode ( ' ', array_map ( fn ( $ext ) => "$ext=" . ( padFormatFind ( $ext, 'read' ) ?: '-' ), [ 'md', 'jsonl', 'yml', 'tab', 'vcard', 'rss', 'nope' ] ) );

?>
