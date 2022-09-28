# WordPress Theme Demo

Written for WordPress Version 6.0

## This theme requires the use of the following WordPress Plugins:

- [Advanced Custom Fields](https://www.advancedcustomfields.com/)

- [Manual Image Crop](https://github.com/tomaszsita/wp-manual-image-crop) *(optional)*

- [Members](https://members-plugin.com/)

- [Regenerate Thumbnails](https://alex.blog/wordpress-plugins/regenerate-thumbnails/)

## To activate Google Maps:

- [Generate a Google Maps API](https://developers.google.com/maps/documentation/javascript/get-api-key)

- [Generate a Google Place API](https://developers.google.com/maps/documentation/javascript/get-api-key)

- [Generate a Google Geocoding API](https://developers.google.com/maps/documentation/javascript/get-api-key)

## To ensure function of custom post types, create the following Advanced Custom Fields:

- Event Date
  - Required
  - Date Picker
  - 20220928 Return Format
  - Show IF POST TYPE == Event
- Main Body Content
  - Wysiwyg Editor
  - Show IF POST TYPE == Program
  - Presentation: Position: High (After-title)
- Map Location
  - Required
  - Google Map
  - Show IF POST TYPE == Campus
- Page Banner
  - Page Banner Subtitle
    - Text
  - Page Banner Background Image
    - Image
  - Show IF POST TYPE == Post
  - Show IF POST TYPE != Post
- Related Campus(es)
  - Required
  - Relationship
  - Field Name: related_campus
  - Show IF POST TYPE == Program
- Related Program
  - Relationship
  - Filters: Search
  - Filter by Post Type: Program
  - Show IF POST TYPE == Event
  - Show IF POST TYPE == Professor


