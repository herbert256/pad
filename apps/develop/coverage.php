<?php

  // Template coverage (pad/lib/coverage.php): start and stop a recording of every local
  // request of every application, and read a run back - each template the run read with the
  // share of its tags and branches that ran, the files of the touched applications it never
  // read, and one template with what never ran marked.
  //
  //   ?coverage&start=suites      record from now on into the run 'suites'
  //   ?coverage&stop              stop recording
  //   ?coverage&run=suites        the report of a run
  //   ?coverage&run=suites&file=  one template of it, marked
  //   ?coverage&clear=suites      throw a run away
  //
  // A suite run under coverage: start, run ./ci.sh, stop, read the report. The develop
  // application itself is never recorded.

  $coverDir = DATA . 'coverage/';

  if ( padRequestHas ( 'start' ) )
    padFilePut ( 'coverage/recording', padCoverageName ( padRequest ( 'start' ) ) );

  if ( padRequestHas ( 'stop' ) and file_exists ( $coverDir . 'recording' ) )
    unlink ( $coverDir . 'recording' );

  $coverClear = padRequestHas ( 'clear' ) ? $coverDir . padCoverageName ( padRequest ( 'clear' ) ) . '.jsonl' : '';

  if ( $coverClear !== '' and file_exists ( $coverClear ) )
    unlink ( $coverClear );

  $coverRecording = file_exists ( $coverDir . 'recording' ) ? padCoverageName ( file_get_contents ( $coverDir . 'recording' ) ) : '';

  $coverRuns = [];

  foreach ( padCoverageRuns () as $coverOne )
    $coverRuns [] = [ 'run' => $coverOne ];

  $coverRun    = padRequestHas ( 'run' ) ? padCoverageName ( padRequest ( 'run' ) ) : '';
  $coverFile   = (string) padRequest ( 'file', '' );
  $coverFiles  = [];
  $coverUnread = [];
  $coverSource = '';
  $coverCount  = 0;

  if ( $coverRun and $coverFile ) {

    $coverRead = padCoverageRead ( $coverRun );
    $coverInfo = $coverRead ['files'] [$coverFile] ?? NULL;

    if ( $coverInfo and ! str_contains ( $coverFile, '..' ) and file_exists ( APPS . $coverFile ) ) {
      $coverText   = padFileGet ( APPS . $coverFile );
      $coverItems  = padCoverageMark ( padCoverageItems ( $coverText ), $coverInfo ['tags'] ?? [], $coverInfo ['arms'] ?? [] );
      $coverSource = padCoverageHtml ( $coverText, $coverItems );
      $coverCount  = $coverInfo ['requests'];
    }

  } elseif ( $coverRun ) {

    $coverReport = padCoverageReport ( $coverRun );
    $coverFiles  = $coverReport ['files'];
    $coverCount  = $coverReport ['requests'];

    usort ( $coverFiles, fn ( $a, $b ) => $a ['percent'] <=> $b ['percent'] ?: strcmp ( $a ['file'], $b ['file'] ) );

    foreach ( $coverReport ['unread'] as $coverOne )
      $coverUnread [] = [ 'unread' => $coverOne ];

  }

  unset ( $coverOne, $coverRead, $coverInfo, $coverText, $coverItems, $coverReport );

  $title = 'Template coverage';

?>
