<?php

  // journey - The steps a person takes, how each felt and who took it, the Mermaid way:
  // title text, section text, and a step a line - Make tea: 5: Me, Cat - its score from 1
  // (bad) to 5 (good). Cards in a row under the bands of their sections, a face at the
  // height of every score, a dot for every person. A first line journey names the kind.
  // Options: title - the title line, else User journey.

  include_once PAD . 'diagrams/_lib/journey.php';

  return padDiagramJourney ( padDiagramSource (), (string) padTagParm ( 'title', padDiagramJourneyTitle ( padDiagramSource () ) ) );

?>
