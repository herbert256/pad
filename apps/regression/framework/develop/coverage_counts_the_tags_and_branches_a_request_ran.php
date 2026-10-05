<?php

  $coverRun    = 'fw' . padRandomString ( 8 );
  $coverTarget = 'regression/framework/develop/a_page_whose_coverage_is_recorded.pad';

  padCurl ( $padHost . "regression/framework/?develop/a_page_whose_coverage_is_recorded&padInclude&padCoverage=$coverRun" );

  $coverRead  = padCoverageRead ( $coverRun );
  $coverInfo  = $coverRead ['files'] [$coverTarget] ?? [];
  $coverItems = padCoverageMark ( padCoverageItems ( padFileGet ( APPS . $coverTarget ) ), $coverInfo ['tags'] ?? [], $coverInfo ['arms'] ?? [] );

  $covered = [];

  foreach ( $coverItems as $coverItem )
    $covered [] = [ 'line' => $coverItem ['line'],
                    'what' => $coverItem ['text'] . ( ( $coverItem ['arm'] ?? '' ) == 'none' ? ' (none held)' : '' ),
                    'hit'  => $coverItem ['hit'] ];

  @unlink ( DATA . "coverage/$coverRun.jsonl" );

?>
