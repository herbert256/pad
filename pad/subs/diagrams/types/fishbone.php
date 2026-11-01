<?php

  // fishbone - The possible causes of an effect, as Ishikawa drew them: the effect in a head
  // on the right, a spine running into it, and a bone for every category of causes, the
  // hierarchy read from the indentation the way Mermaid's ishikawa reads it - the first line
  // the effect, a line indented under it a category (Methods, Machines, People ...), a line
  // under a category a cause, and further in a cause of that cause. The bones stand above
  // and below the spine by turns, every one leaning toward the head, a cause a rib along its
  // text; each bone reads from its category toward the spine. A first line fishbone, or
  // Mermaid's ishikawa-beta, names the kind. Options: title - Fishbone diagram unless given.

  include_once PAD . 'subs/diagrams/_lib/fishbone.php';

  return padDiagramFishbone ( padDiagramSource (), (string) padTagParm ( 'title', 'Fishbone diagram' ) );

?>
