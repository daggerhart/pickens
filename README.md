# Pickens

A simple CMS written with Slim that uses markdown files for content. Inspired by jekyll.

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
        
        
## Internal Route Cache Item

#### Required 

Property | Description 
---|---
`title` | Full title for the content
`description` | General description of the content
`slug` | Unique machine-safe ID for the content. Doubles for route url.
`content_raw` | Un-parsed content 
`content` | Parsed content ready for templating

#### Optional

Property | Description
---|---
`date` | Date the content was created
`category` | Single term within taxonomy "category"
`tags` | Comma separated list of terms within the taxonomy "tags" 
`image` | Local file path or full url to an image asset file

#### Generated

Property | Description
---|---
`type` | Collection content belongs to
`type_slug` | Slug for the collection content belongs to
`alias` | Content's uri alias
`filepath` | Full filepath to the content
`filename` | File name for the content
`filedir` | Directory content can be found int
`file_slug` | File name without the file extension
`template` | Found template for this content 

## Middleware

Middleware should test the request, and alter the response accordingly.

Profile:  $request, $response, $next (callable)

#### Build internal route cache

* Scan content files
* Store their metadata in an easily searchable cache
* Key by internal route

#### Find the appropriate content & load it

Alias | Internal Route | Description
---|---|---
`/<collection-slug>` | `/collection/<collection-slug>` |  Serve a <setting> amount of a collection
`/<page-slug>` | `/page/<page-slug>` | Serve a single page 
`/post/<post-slug>` | `/post/<date>_<post-slug>` | Serve a single post 
`/<taxonomy-slug>/<taxonomy-term-slug>` | `/taxonomy/term/<term-slug>` | Serve a <setting> amount of posts belonging to a taxonomy term
`/api/<command>/<internal-route>` | `/<internal-route>` | <command> (get) - Return json objects according to the internal route  
`/` | `/page/home` | Required homepage markdown file `content/pages/home.md`

**Examples**

Alias | Internal Route | Description
---|---|---
`/posts` | `/collection/posts` | List of <setting> posts 
`/my-first-page` | `/page/my-first-page` | A page with a filename following this pattern: `content/pages/my-first-page.md`
`/post/howdy-world` | `/post/<post-date>_howdy-world` | The post with a filename following this pattern: `content/posts/<post-date>_howdy-world.md`  
`/category/birds` | `/taxonomy/term/birds` | The content within the taxonomy "category" and the term "birds"
`/movies` | `/collection/movies` OR `/page/movies` | List of a custom collection named "movies" OR a page named "movies" 

#### Load the requested content

Type | Description
---|---
html | Send an html response by first passing the content through the templating engine
json | Send a json response containing an array of the requested content


#### Template html response

Internal Route | Template | Description
---|---|---
`/collection/<collection-slug>` | `collection-<collection-slug>.html` | How an item in a specific collection is displayed in a list. 
`/page/<page-slug>` | `page-<page-slug>.html` | Template file for a single specific page
`/page/<*>` | `page.html` | Generic page template
`/post/<post-slug>` | `post-<post-slug>.html` | Specific post template
`/post/<*>` | `post.html` | Generic post template
`taxonomy/term/<term-slug>` | `term-<term-slug>.html` | Specific term template
`taxonomy/term/<*>` | `term.html` | Generic term template


Examples:

* `page-home.html` - Custom homepage template 
* `collection-posts.html` - The main list of posts  
* `term-birds.html` - Specific template for the content in the "birds" term 
* `collection-movies.html` -  Template for content in the "movies" collection


#### Deliver the output 

Slim should handle the delivered object.  I may need "negotiation" somewhere in the mix