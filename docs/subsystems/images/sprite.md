# sprite

Render a named rectangle from an application-owned atlas using one shared browser image URL. `manifest` names a page array (the PHP helper accepts the array directly): `source` is the local image path, `dpr` 1..4, `sprites` maps names to integer `[x,y,width,height]` source-pixel rectangles. The SVG viewBox clips the atlas and its displayed dimensions divide the crop by DPR.

`{image:sprite "harbour", manifest="atlas", alt="Harbour detail"/}`. At most 512 crops; every requested crop is bounds checked. Uses the ordinary realpath-contained image source policy and 64-megapixel limit; normalise EXIF orientation before making an atlas. Alt text is required, empty for decoration. No generated image or inline script is used.
