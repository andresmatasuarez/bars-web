# Raw Source Files

This directory contains original/raw source files for reference only. These files are **not** used directly by the theme or build system.

Production-ready versions (resized, optimized) should be placed in `shared/resources/`.

## Edition moon

Each year's poster comes with its own "moon with the obelisk". The site shows the current edition's moon in the bars2026 header, and each film's share image shows the moon of the film's edition. Editions without one use the classic moon.

### What to make

A PNG of the moon with the obelisk:

- transparent background, with no white edges around the splatter or the obelisk;
- the round part centered, both from top to bottom and from left to right;
- 256 px tall. The site shows it 32 px tall (header on phones), 44 px tall (header on desktop) and 53 px tall (share images).

The obelisk can stay see-through. On the dark header it looks almost black.

### Steps

The source file changes every year. It may have separate layers or be a flat image, with a white or a dark background, and with text such as "BARS 27" on it. Adapt these steps to the file you get:

1. Get the moon on its own. If the file has layers, look for the moon's layer and export only that one. If it is a flat image, remove the text and anything else around the moon.
2. Make the background transparent. If you have to remove a white background from a flat image, check the soft edges of the splatter at full size: they must not keep a white fringe.
3. If long splatter on one side pushes the round part off center, cut it, and fade out the last stretch before the cut so the cut lines thin out instead of ending in a straight edge. If needed, add transparent space on the other side so the round part is centered.
4. Export it 256 px tall, with metadata removed (`-strip` in ImageMagick), as `shared/resources/barsYYYY/barsYYYY_luna.png`. Keep the source file in `shared/raw/barsYYYY/`.
5. Check it at 32 and 44 px tall on the site's background color (`#0a0a0a`): the round part and the obelisk must still be clear.
6. Add `"moon": "resources/barsYYYY/barsYYYY_luna.png"` to the edition in `shared/editions.json`.
7. Check a film's share image. If films of that edition already had share images, raise `BARS_OG_VERSION` in `themes/bars2026/php/og-image.php`. After the deploy, the server deletes the old share images by itself and makes new ones.

### Example: 2026 (edition 27)

The source is `bars2026/BARS27_LOGOchico.psd`. The moon is alone on the layer "LUNA copy" (index 2), already on a transparent background. The white background and the "BARS 27" text are other layers. The trimmed layer is 1298×1228 px, with the round part centered at (663, 556). The splatter below it was longer than above it, so the image was cut at y = 1112 (twice 556), with the last 110 px fading out, and 28 px of transparent space were added on the right:

```bash
mkdir -p .qa/moon
magick 'shared/raw/bars2026/BARS27_LOGOchico.psd[2]' -trim +repage .qa/moon/full.png
magick -size 1298x1112 xc:black -fx "clamp((1112-j)/110)" .qa/moon/fade.png
magick .qa/moon/full.png -crop 1298x1112+0+0 +repage \( .qa/moon/fade.png -alpha copy \) \
  -compose DstIn -composite -compose Over -background none -gravity west -extent 1326x1112 \
  -resize x256 -strip PNG32:shared/resources/bars2026/bars2026_luna.png
```

To list a PSD's layers: `identify -format "%s %[label]\n" file.psd`.
