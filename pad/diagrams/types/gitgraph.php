<?php

  // gitgraph - The commits of a repository on their branches, the branches leaving and the
  // merges joining, the Mermaid way: commit (id: "name", tag: "v1", type: HIGHLIGHT or
  // REVERSE), branch name (order: n), checkout name or switch name, merge name and
  // cherry-pick id: "name"; main is the branch at the start. A lane a branch, a column a
  // commit, drawn left to right. A first line gitGraph names the kind. Options: title -
  // Commit graph unless given.

  include_once PAD . 'diagrams/_lib/gitgraph.php';

  return padDiagramGitgraph ( padDiagramSource (), (string) padTagParm ( 'title', 'Commit graph' ) );

?>
