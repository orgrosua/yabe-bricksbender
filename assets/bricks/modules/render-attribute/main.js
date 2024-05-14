/**
 * @module render-attribute
 * @package Yabe Bricksbender
 * @since 1.0.1
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 * 
 * Render the custom attribute on the Bricks editor's canvas.
 */

import { brxIframe } from '../../constant.js';
import { logger } from '../../logger.js';

const bindTmpl = `v-bind="{...(settings._attributes ? Object.assign({}, ...settings._attributes.map(attr => ({ [attr.name]: attr.value ?? '' }))) : {})}"`;

const xTemplates = brxIframe.contentDocument.querySelectorAll('script[type="text/x-template"][id^="tmpl-bricks-element-"]');
xTemplates.forEach(element => {
    if (element.id === 'tmpl-bricks-element-text-basic') {
        element.innerHTML = element.innerHTML.replace('<contenteditable', `<contenteditable ${bindTmpl}`);
    }

    if (element.id === 'tmpl-bricks-element-heading') {
        element.innerHTML = element.innerHTML.replace('<component', `<component ${bindTmpl}`);
    }
});

logger('Module loaded!', { module: 'render-attribute' });
