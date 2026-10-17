<?php

  // How long ago a moment was, as the page shows it - the {timeago} tag.
  //
  //   {timeago $created}        <time datetime="2026-10-08T09:10:00+02:00"
  //                             title="Thursday 8 October 2026, 09:10">5 minutes ago</time>
  //
  // The words are padAgo's (lib/date.php) - the ago pipe's - and the <time> around them
  // gives the moment to a machine in its datetime and to a reader, on hover, in its title:
  // '3 weeks ago' alone does not say which day.

  function padTimeago ( $moment, $now, $format ) {

    return '<time class="pad-timeago" datetime="' . padChartAttr ( $moment->format ( DATE_ATOM ) ) . '"'
         . ' title="' . padChartAttr ( $moment->setTimezone ( padDateZone () )->format ( $format ) ) . '">'
         . padChartAttr ( padAgo ( $moment, $now ) ) . '</time>';

  }

?>
