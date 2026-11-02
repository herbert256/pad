# Avatars

`avatar:kind` renders an identity picture. Kinds are files in `types/`.

| Kind | Example | Behaviour |
| --- | --- | --- |
| initials | `{avatar:initials 'Ada Lovelace', size=48, shape='square'/}` | Unicode initials with a stable accessible palette; `label=` overrides the name. |
| identicon | `{avatar:identicon 'ada@example.org', title='Ada'/}` | Stable symmetric SVG without any outside request. |
| gravatar | `{avatar:gravatar 'ada@example.org', fallback='avatar', alt='Ada'/}` | SHA-256 Gravatar URL, 2x image, lazy load and no referrer. `fallback='avatar'` or `'identicon'` renders locally without contacting Gravatar. |
| geometric | `{avatar:geometric 'ada', style='mosaic', label='Ada'/}` | Stable `rings` (default), `mosaic` or `beam` artwork. |
| photo | `{avatar:photo 'photos/ada.jpg', alt='Ada', fallback='initials'/}` | A local photo or HTTP(S) URL. `fallback` may be `initials`, `identicon` or `geometric`; `shape` is `circle` or `square`. |
| rings | `{avatar:rings 'account-17', label='Ada'/}` | Concentric arcs, with an optional 2–16 colour `palette`. |
| mosaic | `{avatar:mosaic 'account-17', label='Ada'/}` | A 5 by 5 triangle mosaic, distinct from geometric's 4 by 4 style. |
| pixelbot | `{avatar:pixelbot 'account-17', label='Ada'/}` | A small robot whose colours and features follow its identity. |

All kinds accept `size=`. Photo and geometric sizes are bounded at 2048 pixels. Text is escaped. Local photo paths remain inside `www/<application>/`, including after symlink resolution; missing or unsafe sources render the generated fallback. Remote URLs are loaded by the browser with no referrer; the server never downloads them. A failed remote image is removed by a shared CSP nonce-aware error listener, revealing the fallback; JavaScript is required for that error recovery. Choose a generated kind when no remote requests are allowed.

The old PAD tags and tag helper paths have been removed. Existing templates migrate to `avatar:initials`, `avatar:identicon`, and `avatar:gravatar`; the existing PHP functions live in this subsystem's `_lib/` files.

`padAvatarMake($kind, $identity, $options)` is the PHP factory for all eight kinds. Its original five kinds (initials, identicon, rings, mosaic, pixelbot) retain a 64-pixel default and 8–1024 bounds; geometric, gravatar and photo use their native 8–2048 bounds. The namespace initials tag retains its existing 48-pixel default. Identicon accepts `title` and its `label` alias; explicit `title` wins. The three new artwork kinds normalize NFC and trim but keep case significant; geometric keeps its separate case-insensitive normalization and artwork.
