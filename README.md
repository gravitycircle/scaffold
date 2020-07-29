
 ![---](http://richardbryanong.com/public/shortcut-icon.png) 

# Angular: Site Scaffolding & Bootstrap v0.91 (WP-stable / Gutenberg Unstable)

This is the starting point of every new site I create using a combination of the following frameworks:

*   **Javascript**
    *   Modernizr
    *   AngularJS v1.3.15
    *   jQuery v2.1.3
*   **Preprocessors**
    *   Compass (requires Ruby and gem installation)
    *   SASS (requires Ruby and gem installation)
    *   PHP v7 or higher (requires running on a server. Preferrably Apache 2.0)
* **Content Management**
  * Wordpress - Customized wp-config.php & config.php
*   **Markup and Styling**
    *   HTML 5
    *   CSS 3
*   **Wordpress Plugin Dependencies**
    *   [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/)
    *   ~~[Classic Editor](https://en-ca.wordpress.org/plugins/classic-editor/)~~
    *   ~~[WP DB Migrate](https://en-ca.wordpress.org/plugins/wp-migrate-db/)~~

Please make sure your config.php is set up to the right Base address by changing the BASE and CANONICAL constants. You'll know that you've set it correctly if you see a red logo on the top of this page after a refresh. Currently the constants are set to adapt to the URLs of your files based on where the active Apache directory is.

Also note that BASE refers to the base URL of the site itself. CANONICAL refers to the base URL of any API endpoint you will be using that runs alongside this application. *(Side Note: CANONICAL will be phased out eventually.)*

**Past Changes:**

 - Gutenberg functions now in-sync with the front end data parser.
 - Updated In-Admin Javascript integration with option to upload files.
 - Created new default template for Gutenberg integration and other template options for non-Gutenberg pages.
 - Gutenberg blocks now included in "ACF" sub-field.
 - Form submission automation has been streamlined.
 - Integrated Google ReCaptcha into framework. V2 is currently supported, V3 -- with some edits.
 - Various QoL updates.

**Framework Changes:**
 - Made HTML easy to convert from back to front end for easy template coding.
 - Build CSS Component for WordPress integration as well as customised ".css" files.
 - SASS integration, grid size changes between front and back end to accommodate font size and layout consistency between Gutenberg and FE.
 - Removed style bleeding from content styles into WP Admin styles.
 - Added Template into same file as admin block render for easy transfer.
 - Shortcut Icon now works in Admin!
 - Updated setup instructions.

**Future Update Notes:**
 - Create a way to output HTML in Angular Template format instead of PHP via WP Plugin and inclusions.
 - Streamline setup, currently very clunky. Utilize Grunt / Composer or other automation applications to do this.
 - Create boilerplate animation template for page navigation and URL management on the front end.


#### ~Happy coding!

---

## Setup Instructions
- Download & Extract Wordpress from [wordpress.org](https://wordpress.org "Blog Tool, Publishing Platform, and CMS &mdash; WordPress") into `_bin/` folder
- Create "media" Folder inside `_bin/`
- Install WordPress normally
- Add in `include_once(str_replace('/_bin', '', dirname(__FILE__)).'/setup.php');` in `wp-config.php` right after `define('DB_COLLATE', '');`.
- Activate the theme and all newly installed plugins.
- Update everything from the WordPress admin.
- Delete all extra themes / plugins. We're departing from those.

## Alright Sparky!
- To finish up your initial WordPress setup, you will want to assign all global values before proceeding to prevent error messages from appearing. To do this, log into Wordpress by going into `_bin/wp-admin` on your browser
- Make sure to set your home page, posts page and 404 page display the correct info:
   - Go to Pages and create a Page for a 404 template, and another one for the page to handle posts. We can use the Sample page as your home page for now.
   - Go to *Settings > Reading* and set the *Your Homepage Displays* option to *A Static Page*
   - Define pages on the 2 options presented. Use the Sample page as your home page for now and the posts page created two steps before this. Scoll down and hit *Save Changes*.
   - Go to under *Settings > General* and scoll to the bottom, through the menu box under "Page Displayed when Lost", select your intended page that would contain your 404 template. Before you hit save...
- The correct Google Cloud Platform API must be placed in the database. Add an API Key named `Google Cloud Platform` and a matching API key to the right, then hit *Save Changes*.
- You're all set up!

#### ~Stay Tuned!