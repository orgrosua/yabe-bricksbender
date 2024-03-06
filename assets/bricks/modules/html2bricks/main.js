/**
 * @module html2bricks 
 * @package Yabe Bricksbender
 * @since 1.0.1
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 * 
 * Convert HTML string to Bricks element
 */

import { brxGlobalProp, brxIframe } from '../../constant.js';
import { logger } from '../../logger.js';
import { parse } from './dom2elements.js';

/**
 * Check and request clipboard permission
 * 
 * @see https://web.dev/articles/async-clipboard
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Add-ons/WebExtensions/Interact_with_the_clipboard
 */
async function checkAndRequestClipboardPermission() {
    if (!navigator.permissions) {
        logger('Clipboard permissions not supported', { module: 'html2bricks', type: 'error' });
        return false;
    }

    let clipboardContent = '';

    // clipboard-read
    const readStatus = await navigator.permissions.query({ name: 'clipboard-read', allowWithoutGesture: false });

    if (readStatus.state === 'prompt') {
        logger('Requesting clipboard-read permission', { module: 'html2bricks' });

        clipboardContent = await navigator.clipboard.readText();

        if (readStatus.state !== 'granted') {
            logger('Clipboard-read permission denied', { module: 'html2bricks', type: 'error' });
            return false;
        }
    }

    // clipboard-write
    clipboardContent = await navigator.clipboard.readText();

    const writeStatus = await navigator.permissions.query({ name: 'clipboard-write' });

    if (writeStatus.state === 'prompt') {
        logger('Requesting clipboard-write permission', { module: 'html2bricks' });

        await navigator.clipboard.writeText(clipboardContent);

        if (writeStatus.state !== 'granted') {
            logger('Clipboard-write permission denied', { module: 'html2bricks', type: 'error' });
            return false;
        }
    }

    return true;
}

async function htmlPasteHandler() {
    if (!await checkAndRequestClipboardPermission()) {
        brxGlobalProp.$_showMessage('[Bricksbender] Clipboard access not available');
        return;
    }

    const clipboardText = (await navigator.clipboard.readText()).trim();

    if (!clipboardText || clipboardText.charAt(0) !== '<') {
        logger('Pasted content is not HTML', { module: 'html2bricks', type: 'error' });
        brxGlobalProp.$_showMessage('[Bricksbender] Pasted content is not HTML');
        return;
    }

    // parse HTML string to DOM
    const doc = (new DOMParser()).parseFromString(clipboardText, 'text/html').body;

    // convert DOM to Bricks element
    const bricksElements = parse(doc);

    const bricksData = {
        content: bricksElements,
        source: 'bricksCopiedElements',
        sourceUrl: window.bricksData.siteUrl,
        version: window.bricksData.version,
        globalClasses: [],
        globalElements: [],
    };

    // copy to clipboard
    await navigator.clipboard.writeText(JSON.stringify(bricksData, null));

    brxGlobalProp.$_pasteElements();

    brxGlobalProp.$_showMessage('[Bricksbender] HTML pasted');

    // restore clipboard content
    await navigator.clipboard.writeText(clipboardText);
}

/**
 * Convert HTML string to Bricks element
 *
 * Windows: Ctrl + Shift + V
 * Mac: Cmd + Shift + V
 */
document.addEventListener('keydown', (event) => {
    if (event.target.id === 'bricks-toolbar' || event.target.id === 'bricks-panel') {
        return;
    }

    if (!(event.ctrlKey || event.metaKey) || !event.shiftKey || event.key.toLowerCase() !== 'v') {
        return;
    }

    htmlPasteHandler();
});

// insert "Paste HTML" menu item after "Paste" menu item
const pasteItemContextMenu = document.querySelector('#bricks-builder-context-menu li:nth-child(2)');
const pasteMenu = document.createElement('li');
pasteMenu.id = 'bricksbender-html2bricks-context-menu';
pasteMenu.classList.add('sep');
pasteMenu.innerHTML = '<span class="label">Paste HTML</span><span class="shortcut">CTRL + SHIFT + V</span>';
pasteMenu.addEventListener('click', htmlPasteHandler);

pasteItemContextMenu.classList.remove('sep');
pasteItemContextMenu.insertAdjacentElement('afterend', pasteMenu);

// TODO: implement iframe support

// brxIframe.contentDocument.addEventListener('keydown', (event) => {
//     if (!(event.ctrlKey || event.metaKey) || !event.shiftKey || event.key.toLowerCase() !== 'v') {
//         return;
//     }

//     htmlPasteHandler();
// });

// const pasteItemContextMenuIframe = brxIframe.contentDocument.querySelector('#bricks-builder-context-menu li:nth-child(2)');
// const pasteMenuIframe = pasteMenu.cloneNode(true);
// pasteMenuIframe.addEventListener('click', htmlPasteHandler);

// pasteItemContextMenuIframe.classList.remove('sep');
// pasteItemContextMenuIframe.insertAdjacentElement('afterend', pasteMenuIframe);

logger('Module loaded!', { module: 'html2bricks' });
