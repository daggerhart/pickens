# Pickens

A simple File Thingy :tm: written with Slim that allows for editing markdown files.

This is a dumb experiment in creating a self contained PHP app.  Configure it to look for files in a specific directory, then you can edit markdown files within the app.

Originally I imagined using this as a note-taking app, while storing my notes in Dropbox. But, meh.

![Slim Pickens](https://upload.wikimedia.org/wikipedia/en/7/73/Slim-pickens_riding-the-bomb_enh-lores.jpg)



### TODO

* Meta Data refactor
    * Markdown
    * Images
    * Videos
* View
    * Images
    * Videos
* Edit
    * Text
    * HTML
    * Markdown
    * Images
* Filesystems
    * Local - Save files locally
    * AWS - Push / sync a file to s3
    * Gist - Push / sync a file to a gist.
* UI
    * Main list page shouldn't be recursive
* API
    * GET - file / dir / all
    * POST - file
* Server
    * Start & Stop
* Config
    * Ignore patterns
