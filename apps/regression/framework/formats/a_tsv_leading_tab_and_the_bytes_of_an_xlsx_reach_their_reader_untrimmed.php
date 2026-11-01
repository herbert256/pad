<?php

  // In TSV a leading tab is an empty cell, not white space: a crosstab whose first header
  // cell is empty reads as its rows, and so does the writer's own text of a column named ''
  // and a text where every line opens with a tab - each by the helper and as {data} with
  // format='tsv', indented as a template writes it - where the trim of a data text took the
  // first line's tab ("a row of 3 cells under a header of 2 names") and the cut of the indent
  // the lines share took the first column of all of them. An xlsx handed to padData as its
  // bytes reads too, where the trim took the zip's closing nulls.

  $tsvCross = "\tQ1\tQ2\nNorth\t1\t2\nSouth\t3\t4";
  $tsvOwn   = padFormatWrite ( 'tsv', [ [ '' => 'North', 'Q1' => 1 ] ] );
  $tsvAll   = "\tname\n\tann\n\tbob";

  $tsvShow = fn ( $rows ) => json_encode ( $rows );

  $tsvRead = implode ( ' | ', [ $tsvShow ( padFormatRead ( 'tsv', $tsvCross ) ),
                                $tsvShow ( padFormatRead ( 'tsv', $tsvOwn ) ),
                                $tsvShow ( padFormatRead ( 'tsv', $tsvAll ) ),
                                $tsvShow ( padData ( $tsvCross, 'tsv' ) ) ] );

  $tsvBook = padFormatWrite ( 'xlsx', [ [ 'a' => 1, 'b' => 'x' ] ] );
  $tsvXlsx = $tsvShow ( padData ( $tsvBook, 'xlsx' ) );

?>
