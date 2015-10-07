# Pickens

A simple CMS written with Slim that uses markdown files for content. Inspired by the simplicity of github pages.

![Slim Pickens](https://upload.wikimedia.org/wikipedia/en/7/73/Slim-pickens_riding-the-bomb_enh-lores.jpg)


### TODO


* Configuration
    * ini vs json
    * Collections
* Metadata - Default expectations of metadata associated with content/post types
    * Title
    * Description
    * Tags
    * Category
    * Image
    * Slug (file name)
* Taxonomies Metadata
    * Name
    * Slug
* Collections
    * Register collection in configuration
    * Collect collections
    * Filter &/ Reduce collection by metadata &/ taxonomy
* Template inheritance
    * Layout per collection type
    * Full content per collection type
    * Teaser per collection type
* Filesystem
    * Private
        * App
        * Configuration
        * Data &/ Content
    * Public
        * Assets
        