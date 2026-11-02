# avatars: index

`padAvatarMake($kind, $identity, $options)` renders local SVG. Kinds: initials,
identicon, rings, mosaic, pixelbot. `{avatar:rings 'account-17', label='Ada'/}` is the
namespace form. Options: size (8–1024), label, palette (2–16 #rrggbb colors); shape
for initials. New styles normalize Unicode NFC and trim; identity remains case-sensitive.
They never expose the identity, only an explicit label (default Avatar). Visual identity
is not authentication. `padAvatar` / `padIdenticon` and their core tags keep existing
byte-for-byte rendering and normalization. New styles do not make network requests.
