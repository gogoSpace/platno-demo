# Demo imagery

These three local images were generated for the Platno demonstration. They show fictional scenes and products, not commissioned client work or stock photography. No external image service or hotlinked asset is needed when the demo runs.

| File | Scene and prompt summary |
| --- | --- |
| `public/images/atelier.jpg` | A sunlit brutalist design atelier, a travertine table and a cobalt blue chair; restrained architectural photography in a wide 3:2 composition. |
| `public/images/story.jpg` | A pale limestone coastal house above Mediterranean cliffs and a blue sea; quiet travel photography in a wide 3:2 composition. |
| `public/images/object.jpg` | A cobalt ceramic pitcher with a loop handle and an ivory bowl on limestone plinths; considered product photography in a wide 3:2 composition. |

The images are original AI-generated demonstration assets and are included with this project's MIT-licensed distribution. There are no stock-photo attribution requirements. The template names, studio, journal, product brand and accompanying stories are fictional examples.

`App\Services\DemoContent` imports the selected local image through Platno's `AssetLibrary::upload()` into the workspace's configured private Laravel disk. Page documents contain the managed asset identifier. The image source is served through Platno's editor route for the canvas and its publication-checked route for public pages. Visitor edits therefore do not replace these source JPEGs or another workspace's media.

The marketing site may display the source files as template thumbnails. The actual editable page uses its own managed copy, not the thumbnail URL.
