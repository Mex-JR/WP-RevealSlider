# Reveal Slider

A powerful and user-friendly WordPress plugin for creating customizable before and after image comparison sliders with drag functionality.

[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue.svg)](https://wordpress.org/plugins/)
[![Version](https://img.shields.io/badge/Version-1.0.0-green.svg)](https://github.com/Mex-JR/WP-Webpage-Slider)
[![License](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## Description

Reveal Slider is a robust WordPress plugin that allows you to easily create and embed interactive "before and after" image comparison sliders on your website. Perfect for photographers, designers, contractors, and anyone who wants to showcase transformations, improvements, or comparisons in an engaging way.

### Key Features

* **Easy Image Upload**: Upload before and after images directly from your WordPress media library
* **Drag & Drop Interface**: Interactive slider with smooth drag functionality
* **Multiple Orientations**: Choose between horizontal and vertical slider orientations
* **Customizable Controls**: Three control types - arrows, line, and hover
* **Responsive Design**: Fully responsive and touch-friendly for all devices
* **Shortcode Support**: Simple shortcode `[reveal_slider id="1"]` for easy embedding
* **Admin Dashboard**: User-friendly admin interface for managing all your sliders
* **Custom Labels**: Add custom text labels for before and after images
* **Position Control**: Set initial slider position (0-100%)
* **Multiple Sliders**: Create unlimited number of sliders
* **Performance Optimized**: Lightweight and fast-loading
* **Gutenberg Block Support**: Full integration with WordPress block editor

### Perfect For

* **Photographers**: Showcase photo editing results
* **Interior Designers**: Display room transformations
* **Contractors**: Show before/after project results
* **Landscapers**: Demonstrate garden makeovers
* **Web Designers**: Present design iterations
* **Fitness Trainers**: Show client progress
* **Real Estate**: Property renovations and staging

## Installation

### Automatic Installation

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Search for "Reveal Slider"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin zip file from the [GitHub repository](https://github.com/Mex-JR/WP-Webpage-Slider)
2. Upload the `reveal-slider` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

### Requirements

* WordPress 5.0 or higher
* PHP 7.2 or higher
* Modern web browser with JavaScript enabled

## Usage

### Basic Setup

1. Install and activate the plugin
2. Go to **Reveal Sliders** in your WordPress admin menu
3. Click **"Add New"** to create your first slider
4. Upload your before and after images
5. Customize the settings (labels, orientation, controls, etc.)
6. Copy the generated shortcode
7. Paste the shortcode anywhere on your site

### Shortcode Usage

Use the following shortcode to embed your slider:

```php
[reveal_slider id="1"]
```

Replace `1` with your actual slider ID.

### Gutenberg Block

The plugin includes full Gutenberg block support for easy integration with the WordPress block editor:

1. In the block editor, click the **+** button to add a new block
2. Search for "Reveal Slider"
3. Select your desired slider from the dropdown
4. The slider will be embedded in your content

### Customization Options

#### Slider Settings

* **Slider Name**: Give your slider a descriptive name for easy identification
* **Before/After Images**: Upload or select images from your media library
* **Labels**: Customize the text labels for before and after images
* **Initial Position**: Set the starting position of the slider handle (0-100%)
* **Orientation**: Choose between horizontal or vertical slider layout
* **Control Type**: Select from three control options:
  - **Arrows**: Default arrows on the handle for dragging
  - **Line**: Simple draggable line without arrows
  - **Hover**: Slider moves on mouse hover (desktop only)

## Screenshots

1. **Admin Dashboard** - Overview of all created sliders with management options
2. **Add/Edit Interface** - User-friendly form for creating and customizing sliders
3. **Image Upload** - Easy media library integration for image selection
4. **Customization Options** - Comprehensive settings panel
5. **Frontend Display** - Horizontal slider in action
6. **Vertical Orientation** - Alternative slider layout
7. **Mobile Responsive** - Touch-friendly interface on mobile devices

## Frequently Asked Questions

### How do I add a slider to my page?

After creating a slider in the admin dashboard, you'll get a shortcode like `[reveal_slider id="1"]`. Simply copy and paste this shortcode into any page, post, or custom post type.

### Can I have multiple sliders on one page?

Yes! You can create as many sliders as you need and embed them anywhere on your site using their respective shortcodes.

### Is the slider responsive?

Yes, Reveal Slider is fully responsive and works perfectly on all devices including mobile phones and tablets.

### Can I customize the appearance?

Yes, you can customize:
- Slider orientation (horizontal/vertical)
- Control type (arrows, line, hover)
- Label text and positioning
- Initial slider position
- Handle styling through CSS

### Does it support touch devices?

Absolutely! The slider includes full touch and swipe support for mobile devices.

### Can I use images from external URLs?

Yes, you can use both uploaded images from your media library and external image URLs.

### How do I change the slider labels?

In the slider settings, you can customize both the "Before" and "After" labels to match your content.

### Can I set a default position for the slider?

Yes, you can set the initial position (0-100%) where the slider handle starts when the page loads.

## Development

### Project Structure

```
reveal-slider/
├── reveal-slider.php          # Main plugin file
├── readme.md                  # This file
├── readme.txt                 # WordPress.org readme
├── Project_Plan.md           # Development planning document
├── includes/
│   ├── class-reveal-slider.php # Main plugin class
│   └── functions.php          # Utility functions
├── assets/
│   ├── css/
│   │   ├── style.css         # Frontend styles
│   │   └── admin.css         # Admin interface styles
│   └── js/
│       ├── script.js         # Frontend JavaScript
│       └── admin.js          # Admin JavaScript
├── templates/
│   └── slider-template.php   # Frontend HTML template
└── languages/                # Translation files
```

### Hooks and Filters

The plugin provides several WordPress hooks for customization:

#### Actions

- `reveal_slider_init` - Fired when the plugin is initialized
- `reveal_slider_admin_menu` - Fired when admin menu is created
- `reveal_slider_enqueue_scripts` - Fired when scripts are enqueued

#### Filters

- `reveal_slider_shortcode_atts` - Filter shortcode attributes
- `reveal_slider_template_vars` - Filter template variables
- `reveal_slider_default_settings` - Filter default slider settings

### Customization Examples

#### Custom CSS

Add custom styles to your theme's stylesheet:

```css
.reveal-slider-handle {
    background-color: #your-color;
}

.reveal-slider-label {
    font-family: 'Your Font', sans-serif;
}
```

#### Custom JavaScript

Extend slider functionality:

```javascript
jQuery(document).ready(function($) {
    $('.reveal-slider-wrapper').on('sliderMove', function(event, position) {
        console.log('Slider moved to position: ' + position);
    });
});
```

## Changelog

### 1.0.0
* Initial release
* Basic before/after slider functionality
* Admin dashboard for slider management
* Shortcode support
* Responsive design
* Touch support
* Multiple customization options
* Gutenberg block integration

## Upgrade Notice

### 1.0.0
Initial release of Reveal Slider plugin.

## Contributing

We welcome contributions! Please feel free to:

1. Report bugs and issues on our [GitHub repository](https://github.com/Mex-JR/WP-Webpage-Slider/issues)
2. Submit pull requests with improvements
3. Suggest new features
4. Help with documentation

### Development Setup

1. Clone the repository: `git clone https://github.com/Mex-JR/WP-Webpage-Slider.git`
2. Install dependencies (if any)
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For support, bug reports, or feature requests, please:

- Check the [FAQ section](#frequently-asked-questions) first
- Visit our [GitHub Issues](https://github.com/Mex-JR/WP-Webpage-Slider/issues) page
- Contact us through our website: [Flowfunnel](https://flowfunnel.io)

## Credits

This plugin was developed by [Flowfunnel](https://flowfunnel.io).

Special thanks to:
- WordPress community for the amazing platform
- All contributors and testers

## License

This plugin is licensed under the **GPL v2 or later**.

```
Reveal Slider WordPress Plugin
Copyright (C) 2024, Flowfunnel

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
```

## Links

- [Plugin Homepage](https://github.com/Mex-JR/WP-Webpage-Slider)
- [WordPress.org Plugin Page](https://wordpress.org/plugins/reveal-slider/)
- [Developer Website](https://flowfunnel.io)
- [GitHub Repository](https://github.com/Mex-JR/WP-Webpage-Slider)
- [Issue Tracker](https://github.com/Mex-JR/WP-Webpage-Slider/issues)

---

**Made with ❤️ for the WordPress community**
