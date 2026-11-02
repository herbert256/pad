<?php
  // The browser receives this exact runtime list through the client argument hook.
  $zones = DateTimeZone::listIdentifiers ( DateTimeZone::ALL );
  return [
    'check' => fn ( $value ) => in_array ( $value, $zones, TRUE ),
    'client' => fn ( $argument ) => json_encode ( $zones ),
    'version' => timezone_version_get (),
    'message' => ':label must be a supported timezone identifier'
  ];
?>
