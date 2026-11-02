<?php
include_once __DIR__ . '/initials.php';
include_once __DIR__ . '/identicon.php';
// New styles use NFC, trimmed, case-sensitive identities; existing wrappers keep their rules.
function padAvatarMake ( $kind, $identity, $options = [] ) {
  if ( ! preg_match('/^[a-z]+$/D', $kind) || ! is_file(__DIR__ . "/../types/$kind.php") )
    throw new InvalidArgumentException('Unknown avatar style');
  if (!is_scalar($identity)) throw new InvalidArgumentException('Avatar identity must be scalar');
  $identity = padAvatarName((string)$identity);
  $size = max(8, min(1024, (int)($options['size'] ?? 64)));
  $label = (string)($options['label'] ?? 'Avatar');
  $palette = $options['palette'] ?? ['#235789','#f1b24a','#d1495b','#197278','#f5f1e3'];
  if (!is_array($palette) || count($palette) < 2 || count($palette) > 16) throw new InvalidArgumentException('Palette needs 2 to 16 hex colors');
  foreach ($palette as $color) if (!is_string($color) || !preg_match('/^#[0-9a-f]{6}$/iD',$color)) throw new InvalidArgumentException('Palette colors must be #rrggbb');
  $palette = array_values($palette);
  $hash = hash('sha256',$identity);
  $draw = include __DIR__ . "/../types/$kind.php";
  if ($kind === 'initials' || $kind === 'identicon') return $draw;
  $label = htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  return '<svg xmlns="http://www.w3.org/2000/svg" role="img" class="pad-avatar pad-avatar-'.$kind.'" width="'.$size.'" height="'.$size.'" viewBox="0 0 100 100" aria-label="'.$label.'"><title>'.$label.'</title>'.$draw.'</svg>';
}
?>
