<?php

  $app = $_REQUEST['app'] ?? 'pad';

  define ( 'APP', APPS . "$app/" );

  if ( ! preg_match ( '/^[A-Za-z0-9_]+$/', $app ) ) pad_boot_error ("Invalid name for app: $app");
  if ( ! file_exists ( APP )                      ) pad_boot_error ("Applicaton does not exists: $app");
  if ( ! is_dir ( APP )                           ) pad_boot_error ("Applicaton does not exists: $app");

?>