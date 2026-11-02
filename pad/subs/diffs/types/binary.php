<?php

  // binary - Byte-offset comparison groups contiguous unequal positions; it does not realign after insertions. Returns offset, changed length, beforeLength/afterLength, lowercase hexadecimal previews and previewTruncated. Combined input ≤1 MiB (lower with maxBytes), preview 0..64 bytes per side, default 32. maxRanges caps changed runs at 10000 (a lower positive bound may be supplied). No file reads, decoding or text heuristics. Named diff:binary renders the operation records with escaping.
  //
  // Runnable example: `apps/tags/examples/diff/binary.pad`. Independent fixture: `php tests/subsystems/expansion-diffs-binary.php`.

include_once PAD.'subs/diffs/_lib/binary.php';
return ['compare'=>'padDiffBinary','render'=>fn($a,$b,$o)=>padDiffRecordsHtml(padDiffBinary($a,$b),FALSE,$o['from'],$o['to'])];
