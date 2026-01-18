<?php

  $title = 'PAD Applications';

  // Get the apps directory (parent of current app)
  $appsDir = dirname(APP) . '/';

  $apps = [];

  // Scan for application directories
  $dirs = scandir($appsDir);

  foreach ($dirs as $dir) {

    // Skip hidden files and current/parent dirs
    if ($dir[0] === '.') continue;

    // Skip _ prefixed directories except _common
    if ($dir[0] === '_' && $dir !== '_common') continue;

    $appPath = $appsDir . $dir . '/';

    // Must be a directory
    if (!is_dir($appPath)) continue;

    // Skip self
    if ($dir === 'apps') continue;

    $app = [
      'name' => $dir,
      'path' => $appPath,
      'link' => ($dir === '_common') ? '' : '/' . $dir . '/',
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

  // Sort: _common first, then alphabetically
  usort($apps, function($a, $b) {
    if ($a['name'] === '_common') return -1;
    if ($b['name'] === '_common') return 1;
    return strcasecmp($a['name'], $b['name']);
  });

  $appCount = count($apps);

?>
