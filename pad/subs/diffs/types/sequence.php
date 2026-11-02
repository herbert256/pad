<?php

  // sequence - Lists of unique string/integer identities, or records with key=field. Typed IDs remain distinct. A deterministic longest increasing common subsequence stays in place; other retained IDs are moves, minimizing moved identities. Added/removed neighbors alone do not cause moves. Paths/from are snapshot offsets, not an executable JSON Patch stream. Retained records also emit strict field replacements. ≤10000 entries and shared 20000-value validation bound; O(n log n) alignment. Named diff:sequence renders the operation records through an explicit escaped adapter.
  //
  // Runnable example: `apps/tags/examples/diff/sequence.pad`. Independent fixture: `php tests/subsystems/expansion-diffs-sequence.php`.

include_once PAD.'subs/diffs/_lib/sequence.php';
return ['compare'=>'padDiffSequence','render'=>'padDiffSequenceHtml'];
