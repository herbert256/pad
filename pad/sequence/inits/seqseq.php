<?php

                       $padSeqKind = include 'sequence/inits/seqseq/parm.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/pull.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/prefix.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/tag.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/store.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/type.php';
  if ( ! $padSeqKind ) $padSeqKind = include 'sequence/inits/seqseq/basic.php';
  if ( ! $padSeqKind ) $padSeqKind = 'default';

  $padSeqInfo ['kinds'] [] = $padSeqKind;

  include "sequence/inits/kinds/$padSeqKind.php";
    
?>