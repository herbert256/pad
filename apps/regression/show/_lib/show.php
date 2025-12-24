<?php

  function diff ( $old, $new ) {

    $diff = Diff::toTable(Diff::compare($old,$new));
    $diff = str_replace('</span><br></td>', '</span></td>', $diff);
    $diff = str_replace('<span></span><br><span> </span>', '', $diff);
    $diff = str_replace('<span></span>', '', $diff);
    $diff = str_replace('<span> </span>', '', $diff);
    $diff = str_replace('<span> ', '<span>', $diff);
    $diff = str_replace('<table class="diff">', '', $diff);
    $diff = str_replace('</table>', '', $diff);
    $diff = str_replace('<span>', '', $diff);
    $diff = str_replace('</span>', '', $diff);

    return $diff;

  }

  function cut (&$content, $start, $end) {

    $cut = '';

    $p1 = strpos($content, $start);
    $p2 = strpos($content, $end);

    if ( $p1 !== FALSE and $p2 !== FALSE and $p1 < $p2 ) {

      $part1 = substr ($content, 0, $p1);
      $part2 = substr ($content, $p2+strlen($end) );

      $p1 += strlen($start);

      $cut     = substr ($content, $p1, $p2-$p1);
      $content = $part1 . $part2;

      return $cut;

    }

    $content = '';
    return '';

  }

?>