<?php

  $title = 'PAD Applications';

  // Get the apps directory (parent of current app)
  $appsDir = dirname(APP) . '/';

  $apps = [];

  // Scan for application directories
  $dirs = scandir($appsDir);

  foreach ($dirs as $dir) {

    // Skip hidden files, current/parent dirs, and _ prefixed directories
    if ($dir[0] === '.' || $dir[0] === '_') continue;

    $appPath = $appsDir . $dir . '/';

    // Must be a directory
    if (!is_dir($appPath)) continue;

    // Skip self
    if ($dir === 'apps') continue;

    $app = [
      'name' => $dir,
      'path' => $appPath,
      'link' => '/' . $dir . '/',
      'description' => ''
    ];

    // Try to read README.md and extract Introduction section
    $readmePath = $appPath . 'README.md';

    if (file_exists($readmePath)) {
      $content = file_get_contents($readmePath);

      // Extract Introduction section content
      // Pattern: ## Introduction followed by text until next ## or end
      if (preg_match('/## Introduction\s*\n+(.+?)(?=\n## |\n#|\z)/s', $content, $matches)) {
        $description = trim($matches[1]);
        // Clean up: remove extra whitespace and limit length
        $description = preg_replace('/\s+/', ' ', $description);
        $app['description'] = $description;
      }
    }

    $apps[] = $app;
  }

  // Sort alphabetically by name
  usort($apps, function($a, $b) {
    return strcasecmp($a['name'], $b['name']);
  });

  $appCount = count($apps);

?>
