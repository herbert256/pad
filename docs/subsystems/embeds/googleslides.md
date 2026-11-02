# Google Slides

`{embed:googleslides 'https://docs.google.com/presentation/d/e/2PACX-.../pub', title='Presentation'/}` accepts published `/d/e/2PACX-.../pub` and `/embed` URLs, including auto detection. It rejects ordinary edit/share URLs. The presentation must already have been published by its owner; PAD never changes sharing. Incoming query parameters are discarded and autoplay is disabled. The shared consent facade, keyboard button, accessible title and noscript link are retained; the frame source is `https://docs.google.com`, with no request before activation. The catalogue example uses a synthetic identifier and does not claim to be a live deck.

[Google publishing documentation](https://support.google.com/docs/answer/183965?hl=en). Test: `php apps/regression/subsystems/embed-googleslides.php`.
