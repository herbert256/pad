<?php
include_once PAD . 'subs/avatars/_lib/avatar.php';
$options=[];
foreach(['size','shape','label','palette'] as $key) if (($value=padTagParm($key,NULL))!==NULL) $options[$key]=$value;
return padAvatarMake($padAvatarKind, padParmText($padParm), $options);
?>
