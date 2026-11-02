# avatars: index

`padAvatarMake($kind, $identity, $options)` renders an avatar. Kinds: initials,
identicon, rings, mosaic, pixelbot, geometric, gravatar and photo.
`{avatar:rings 'account-17', label='Ada'/}` is the namespace form.

The original five factory kinds retain size bounds of 8–1024 and a default of 64.
Initials accepts shape (`circle` or `square`) and label; identicon accepts title or
its label alias, with title taking precedence. The namespace initials tag keeps
its 48-pixel default. `padAvatar` and `padIdenticon` keep their existing signatures,
byte-for-byte drawing and normalization. Legacy `pad:avatar`, `pad:identicon` and
`pad:gravatar` tags were replaced by the corresponding `avatar:` namespace tags.

Rings, mosaic and pixelbot accept a label (default Avatar) and a palette of 2–16
#rrggbb colours. They normalize Unicode NFC and trim; identities remain case-sensitive.
They never expose the identity itself and make no network requests. Visual identity
is not authentication. Geometric's rings/mosaic/beam styles retain their separate
case-insensitive artwork; top-level mosaic is a distinct 5 by 5 triangle grid.

The factory also exposes geometric's style and label, Gravatar's default/fallback/alt,
and photo's alt/fallback/shape options. These three kinds use 8–2048 size bounds.
Gravatar's local fallback options make no external request. Photo validates local
paths beneath the app's public directory and supplies a generated fallback; remote
images load only in the browser, with no referrer. See the subsystem README for details.
