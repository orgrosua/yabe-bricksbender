/**
 * @module ko-fi
 * @package Yabe Bricksbender
 * @since 1.0.1
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 * 
 * Add a Ko-fi button to the bricks panel.
 */

import { logger } from '../../logger.js';
import './style.scss';
import toolbar_item from './toolbar-item.html?raw';

const bricksToolbarSelector = '#bricks-toolbar ul.group-wrapper.right';

const coffee = localStorage.getItem('yabe-bricksbender-ko-fi') ?? -1;

if (coffee === -1 || (coffee !== 'done' && coffee !== 'never' && new Date() > new Date(coffee))) {
    // create element from html string
    const koFiButtonHtml = document.createRange().createContextualFragment(`${toolbar_item}`);

    // add the button to the bricks toolbar as the first item
    const bricksToolbar = document.querySelector(bricksToolbarSelector);
    bricksToolbar.insertBefore(koFiButtonHtml, bricksToolbar.firstChild);

    document.getElementById('bricksbender-ko-fi').addEventListener('click', (el) => {
        const date = new Date();
        date.setDate(date.getDate() + 7);
        localStorage.setItem('yabe-bricksbender-ko-fi', date);
        window.open('https://ko-fi.com/Q5Q75XSF7', '_blank');
        document.getElementById('bricksbender-ko-fi').remove();
    });
}

logger('Module loaded!', { module: 'plain-classes' });
