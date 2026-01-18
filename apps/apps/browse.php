<?php

  $title = 'Browse Application';

  // Get the apps directory
  $appsDir = dirname(APP) . '/';

  // Get requested app from query string
  $app = $_GET['app'] ?? '';
  $file = $_GET['file'] ?? '';

  $appPath = '';
  $appFiles = [];
  $source = '';
  $currentFile = '';

  // Validate app name (security: prevent directory traversal)
  if ($app && preg_match('/^[a-zA-Z0-9_-]+$/', $app)) {

    $appPath = $appsDir . $app . '/';

    if (is_dir($appPath)) {

      $title = "Browse: $app";

      // Get list of source files
      $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appPath, RecursiveDirectoryIterator::SKIP_DOTS)
      );

      foreach ($iterator as $fileInfo) {
        $ext = $fileInfo->getExtension();

        // Only show source files
        if (in_array($ext, ['php', 'pad', 'json', 'xml', 'html', 'js', 'css'])) {
          $relativePath = str_replace($appPath, '', $fileInfo->getPathname());
          $appFiles[] = [
            'path' => $relativePath,
            'name' => $fileInfo->getFilename(),
            'ext' => $ext
          ];
        }
      }

      // Sort files by path
      usort($appFiles, function($a, $b) {
        return strcasecmp($a['path'], $b['path']);
      });

      // If a file is requested, load its source
      if ($file && preg_match('/^[a-zA-Z0-9_\-\/\.]+$/', $file)) {
        $filePath = $appPath . $file;

        if (file_exists($filePath) && strpos(realpath($filePath), realpath($appPath)) === 0) {
          $currentFile = $file;
          $source = padColorsFile($filePath);
        }
      }
    }
  }

  $fileCount = count($appFiles);

?>
