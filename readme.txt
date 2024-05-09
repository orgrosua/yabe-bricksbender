=== Yabe Bricksbender ===
Contributors: suabahasa, rosua
Donate link: https://ko-fi.com/Q5Q75XSF7
Tags: bricks builder, tailwind css
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.0.13
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The Bricks builder extension

== Description ==

### Yabe Bricksbender: The Bricks builder extension.

Yabe Bricksbender is a plugin that enhances the Bricks editor with functionalities that are not available in the core plugin to make your workflow more efficient.

### Modules

Yabe Bricksbender is packed full of modules designed to streamline your workflow.

✅ **Plain classes**

Add the ability to write plain CSS classes to Bricks elements without adding the class to the Global Class database.

Features:
- **Auto-complete**: Intelligent suggestions for [Tailwind CSS](https://siul.yabe.land/) class names.
- **Hover Class Name**: Hover the Class Name to preview the generated CSS code.
- **Automatic Class Sorting**: Sort the classes based on the Tailwind CSS [official recommendation](https://tailwindcss.com/blog/automatic-class-sorting-with-prettier).
- **Quick class preview**: Preview CSS classes in the Bricks editor by hovering/navigating without adding.

✅ **HTML to Bricks**

Convert HTML to Bricks elements. Easily convert Tailwind CSS components to Bricks elements.


Visit [our website](https://bricksbender.yabe.land) for more information.

= Love Yabe Bricksbender? =
- Join our [Facebook Group](https://www.facebook.com/groups/1142662969627943)
- Or rate us on [WordPress](https://wordpress.org/support/plugin/yabe-bricksbender/reviews/?filter=5/#new-post) 🙂

= Credits =
- Image by [Freepik](https://www.flaticon.com/free-icon/brick-wall_3769316) on Flaticon

= Contributors & Developers =

Interested in development?
Visit the [GitHub repository](https://github.com/orgrosua/yabe-bricksbender) to get involved.


== Changelog ==

= 1.0.13 =
* **Change**: [plain-classes] Revert the "Hover the Class Name to preview the generated CSS code" feature that introduced in v1.0.8. It's not working as expected and causing performance issues. We will revisit this feature in the future release
* **Change**: [plain-classes] Revert the "Colorized background of the class based on the breakpoint/screen size" feature that introduced in v1.0.8. It's not working as expected and causing performance issues. We will revisit this feature in the future release

= 1.0.12 =
* **New**: [html2bricks] Added the Paste button on the Structure panel header allowing to paste without needing selecting the element first.
* **Fix**: [plain-classes] Freezed when moving the cursor to any line of the plain classes.

= 1.0.11 =
* **Improve**: Reduce the size of plugin file.

= 1.0.10 =
* **Fix**: [plain-classes] revert the Open the class suggestion on text selection [v1.0.6].
* **Improve**: [plain-classes] Hover the Class Name performance.

= 1.0.9 =
* **New**: Yabe Bricksbender is now available on [WordPress.org](https://wordpress.org/plugins/yabe-bricksbender/)

= 1.0.8 =
* **Improve**: [plain-classes] Hover the Class Name to preview the generated CSS code.

= 1.0.6 =
* **Improve**: [plain-classes] Open the class suggestion on text selection.
* **Improve**: [plain-classes] Add border to the text input for better visibility.
* **Improve**: [plain-classes] Colorized background of the class based on the breakpoint/screen size.
* **Improve**: [plain-classes] Automatic Class Sorting with Prettier. Based on the official [Prettier plugin for Tailwind CSS](https://tailwindcss.com/blog/automatic-class-sorting-with-prettier).

= 1.0.5 =
* **Fix**: Comply to the WP.org plugin submission review.

= 1.0.4 =
* **Fix**: [html2bricks] Alpine.js compatibility issue.

= 1.0.3 =
* **Fix**: [html2bricks] Preserve the original tag name.

= 1.0.2 =
* **Fix**: Modules not loading in the Bricks editor.

= 1.0.1 =
* **New**: Introduced a new module, HTML to Bricks.
* **New**: Introduced a new module, Plain Classes.

= 1.0.0 =
* 🐣 Initial release.
