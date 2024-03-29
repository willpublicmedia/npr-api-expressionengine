# NPR Story API ExpressionEngine Module

Wraps NPR's [Story API Wordpress plugin](https://github.com/npr/nprapi-wordpress) in an ExpressionEngine module. By default, installation will create a default channel, required fields, and a publish layout suitable for pushing to and pulling from the [NPR Story API](https://api.npr.org).

The plugin also allows channel mapping, allowing existing channels to push to or pull from the story api.

## Setup

### Install Addon Files

1. Clone the project as `npr_story_api`. Note: ExpressionEngine requires the project directory to be named `npr_story_api`.
2. From the command line, cd to the project directory.
3. Run `php composer.phar install`.

### Basic Configuration

1. (Optional) From the Control Panel's Files interface, create an NPR Images file location and grant privileges to appropriate user groups.
2. From Control Panel > Developer > Addons, install the plugin.
3. Click the NPR Story API settings icon:

- Use your org's API key in the API Key field.
- Use your org's org ID in the Org ID field.
- Set the pull url (<https://api.npr.org> for production, <https://api-s1.npr.org> for NPR's api test environment).
- Set the push url as above. Note: API push requires IP address whitelist by NPR.
- Choose a file location for images from pulled stories.
- Choose channels to be mapped to the NPR api. (Start with the NPR Stories channel.)

4. Save settings.

## Channel Mapping

Mapped channels must have access to all fields required by the story api (NPR Story API field group), and be configured as mapped in the api settings. Channel naming conventions are strongly recommended.

## Using the API

### Pulling a Story

1. Create a new channel entry in a mapped channel.
2. In the publish tab, add any title text.
3. Switch to the Options tab

- Set story source to NPR.
- Set the NPR Story ID to the desired story.
- Click Overwrite Local.

4. Click Save.

The story will be pulled and mapped to expression engine. The title and URL field will be overwritten by the remote story. Note that the URL will only be overwritten when creating a new story, not on edit. This prevents URLs from changing on live stories. All images will be stored in the Files location configured in the API settings.

Formatting changes can be preserved by saving _without_ using the Overwrite Local switch.

If the story is correctly formatted, change story status from Draft to Open and click Save.

### Pushing a Story

1. Enter story as usual. See the [NPR Story API documentation](https://api.npr.org) for guidance on story element use.
2. Switch to the Options tab and set Status to Open.
3. Save the story before pushing to prevent work from being lost on a failed push.
4. Switch to the Options tab:

- Leave story source set to Local.
- Leave NPR Story ID blank.
- Click Publish to NPR.
- _Optional:_ Click Send to NPR One to include the story on NPR One.
- _Optional:_ Click NPR One Featured to request the story be featured in NPR One.

5. Click Save.

If the story was pushed successfully, the story will be updated automatically with the NPR story ID.

To re-push a story, make corrections, update the Publication Date (Date tab), then click Save.

## Dependencies

- ExpressionEngine 6+
- PHP 7+
- PHP curl module
- PHP xml module
- [Composer](https://getcomposer.org)
- [NPR Story API Wordpress Plugin](https://github.com/openpublicmedia/nprapi-wordpress) (via composer)

## Changelog

### 3.0.13

- check for xml before stripping headers

### 3.0.12

- strip http headers from xml response

### 3.0.11

- correct EE7 filemanager reference format

### 3.0.10

- correct audio property checks
- add audio selection fallback
- set top level file manager check (WIP: plugin must use file manager compatibility mode)

### 3.0.9

- improve property checks on story mapping

### 3.0.8

- update openpublicmedia/nprapi-wordpress to v1.9.5.2

### 3.0.7

- check field availability on save

### 3.0.6

- check autofill values before using
- check several styles of error response

### 3.0.5

- update pub_date on save

### 3.0.4

- parse xml from raw npr error response
- check for story ID before exiting push

### 3.0.3

- check for npr response message before exiting push

### 3.0.2

- limit updater_3_0_0 column delete to php 8+

### 3.0.1

- remove type hints incompatible with php7

### 3.0.0

- php 8: correct pass-by-reference warnings
- php 8: correct argument type checking on api response
- php 8: correct required, optional param order
- breaking: remove EE audio columns (audio_type, audio_filesize, audio_format, audio_rights, audio_region, audio_rightsholder)
- breaking: update EE class namespace for v6
- autofill default audio permissions, duration, title
- changed preferred wysiwyg editor to wyvern
- update wordpress package to 1.9.5
- don't delete channels, fields, or statuses on uninstall
- increase column length for pulled story slugs and short_titles
- various: check variable state before using

### 2.1.2

- upgrade wordpress package to 1.9.4

### 2.1.1

- Correct version constant.

### 2.1.0

- Update connection error handling.
- Update install/uninstall methods for EE6 compatibility.
- Follow Open Public Media fork of nprapi-wordpress.
- Pin nprapi-wordpress to 1.9.2.

### 2.0.11

- Process only largest image crop on story pull.
- Make sure sideloaded images get a thumbnail.

### 2.0.10

- Fix grid fieldtype detection warning.

### 2.0.9

- Load required modules.

### 2.0.8

- Correct constant name.
- Update docs url.

### 2.0.7

- Move channel entry data assignment to public utility.

### 2.0.6

- add optional parameters to table loader class methods.

### 2.0.5

- Add config utility class.

### 2.0.4

- Bump version constant.

### 2.0.3

- Roll back 2.0.2.
- Check for invalid `pubDate` and `storyDate` values.

### 2.0.2

- Update `audioRunbyDate` format

### 2.0.1

- Don't display mapped channel error when autofilling media.

### 2.0.0

- Breaking change: Remove required column constraints on `npr_images` and `audio_files` fields.
- Extract field utilities class.
- Autofill audio, image columns from media uploads on all mapped channels.
- On pull, fall back to entry url_title if generated url title is empty.
- Remove page-breaking debug code from pull function.

### 1.0.4

- Add byline splitting parameter to nprml mapper.
- Bugfix: Correct nprml author assignment in empty byline.

### 1.0.3

- Bugfix: Use unique partner ID.

### 1.0.2

- Bugfix: Use site_url + channel comment url + entry title as html permalink tag value on push.
- Push [EE image manipulations](https://docs.expressionengine.com/latest/control-panel/file-manager.html#constrain-or-crop) as nprml image crops.

### 1.0.1

- Check for php extension dependencies on install and update.
