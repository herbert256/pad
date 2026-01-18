<?php

  $title = 'Browse Application';

  // Get the apps directory
  $appsDir = dirname(APP) . '/';

  // Get requested app, directory and file from query string
  $app = $_GET['app'] ?? '';
  $dir = $_GET['dir'] ?? '';
  $file = $_GET['file'] ?? '';

  $appPath = '';
  $appDirs = [];
  $appFiles = [];
  $source = '';
  $currentFile = '';
  $parentDir = '';

  // Validate app name (security: prevent directory traversal)
  if ($app && preg_match('/^[a-zA-Z0-9_-]+$/', $app)) {

    $appPath = $appsDir . $app . '/';

    if (is_dir($appPath)) {

      $title = "Browse: $app";

      // Validate and set current directory
      $currentDir = $appPath;
      if ($dir && preg_match('/^[a-zA-Z0-9_\-\/]+$/', $dir)) {
        $testPath = $appPath . $dir . '/';
        if (is_dir($testPath) && strpos(realpath($testPath), realpath($appPath)) === 0) {
          $currentDir = $testPath;
          $title = "Browse: $app/$dir";

          // Calculate parent directory
          if (strpos($dir, '/') !== false) {
            $parentDir = dirname($dir);
          } else {
            $parentDir = '';
          }
        }
      } elseif ($dir) {
        // Invalid dir, reset
        $dir = '';
      }

      // Scan current directory for files and subdirectories
      $items = scandir($currentDir);

      foreach ($items as $item) {
        // Skip hidden files and . / ..
        if ($item[0] === '.') continue;

        $itemPath = $currentDir . $item;
        $relativePath = ($dir ? $dir . '/' : '') . $item;

        if (is_dir($itemPath)) {
          $appDirs[] = [
            'name' => $item,
            'path' => $relativePath
          ];
        } else {
          $ext = pathinfo($item, PATHINFO_EXTENSION);
          // Only show source files
          if (in_array($ext, ['php', 'pad', 'json', 'xml', 'html', 'js', 'css'])) {
            $appFiles[] = [
              'path' => $relativePath,
              'name' => $item,
              'ext' => $ext
            ];
          }
        }
      }

      // Sort directories and files by name
      usort($appDirs, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
      });
      usort($appFiles, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
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

  $dirCount = count($appDirs);
  $fileCount = count($appFiles);
  $hasParent = ($dir !== '');

?>