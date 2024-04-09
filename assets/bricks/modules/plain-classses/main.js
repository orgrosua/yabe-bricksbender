/**
 * @module plain-classes 
 * @package Yabe Bricksbender
 * @since 1.0.0
 * @author Joshua Gugun Siagian <suabahasa@gmail.com>
 * 
 * Add plain classes to the element panel.
 */

import './style.scss';

import { logger } from '../../logger.js';

import { nextTick, ref, watch } from 'vue';
import autosize from 'autosize';
import Tribute from 'tributejs';

import HighlightInTextarea from './highlight-in-textarea';
import { brxGlobalProp, brxIframeGlobalProp, brxIframe } from '../../constant.js';

const textInput = document.createElement('textarea');
textInput.classList.add('bricksbender-plc-input');
textInput.setAttribute('rows', '3');
textInput.setAttribute('spellcheck', 'false');

const visibleElementPanel = ref(false);
const activeElementId = ref(null);

let twConfig = null;
let screenBadgeColors = [];

(async () => {
    if (brxIframe.contentWindow.tailwind) {
        const tw = brxIframe.contentWindow.tailwind;
        twConfig = await tw.resolveConfig(tw.config);

        // find all colors that value a object and the object has 500 key
        let baseColors = Object.keys(tw.colors).filter((color) => {
            return typeof tw.colors[color] === 'object' && tw.colors[color][500] !== undefined && ![
                "slate",
                "gray",
                "zinc",
                "neutral",
                "stone",
                "warmGray",
                "trueGray",
                "coolGray",
                "blueGray"
            ].includes(color);
        });

        baseColors = baseColors.map((color) => {
            return {
                name: color,
                value: tw.colors[color][500],
            };
        });

        // randomize the base colors
        baseColors.sort(() => Math.random() - 0.5);

        let screenKeys = Object.keys(twConfig.theme.screens);

        for (let i = 0; i < screenKeys.length; i++) {
            screenBadgeColors.push({
                screen: screenKeys[i],
                color: baseColors[i].value,
            });
        }
    }
})();


let hit = null; // highlight any text except spaces and new lines

autosize(textInput);

let autocompleteItems = [];

wp.hooks.addAction('bricksbender-autocomplete-items-refresh', 'bricksbender', () => {
    // wp hook filters. {value, color?, fontWeight?, namespace?}[]
    autocompleteItems = wp.hooks.applyFilters('bricksbender-autocomplete-items', [], textInput.value);
});

wp.hooks.doAction('bricksbender-autocomplete-items-refresh');

const tribute = new Tribute({
    containerClass: 'bricksbender-tribute-container',

    autocompleteMode: true,

    // Limits the number of items in the menu
    menuItemLimit: 30,

    noMatchTemplate: '',

    values: async function (text, cb) {
        const filters = await wp.hooks.applyFilters('bricksbender-autocomplete-items-query', autocompleteItems, text);
        cb(filters);
    },

    lookup: 'value',

    itemClass: 'class-item',

    // template
    menuItemTemplate: function (item) {
        let customStyle = '';

        if (item.original.color !== undefined) {
            customStyle += `background-color: ${item.original.color};`;
        }

        if (item.original.fontWeight !== undefined) {
            customStyle += `font-weight: ${item.original.fontWeight};`;
        }

        return `
            <span class="class-name" data-tribute-class-name="${item.original.value}">${item.string}</span>
            <span class="class-hint" style="${customStyle}"></span>
        `;
    },
});

tribute.setMenuContainer = function (el) {
    this.menuContainer = el;
};

const tributeEventCallbackOrigFn = tribute.events.callbacks;

tribute.events.callbacks = function () {
    return {
        ...tributeEventCallbackOrigFn.call(this),
        up: (e, el) => {
            // navigate up ul
            if (this.tribute.isActive && this.tribute.current.filteredItems) {
                e.preventDefault();
                e.stopPropagation();
                let count = this.tribute.current.filteredItems.length,
                    selected = this.tribute.menuSelected;

                if (count > selected && selected > 0) {
                    this.tribute.menuSelected--;
                    this.setActiveLi();
                } else if (selected === 0) {
                    this.tribute.menuSelected = count - 1;
                    this.setActiveLi();
                    this.tribute.menu.scrollTop = this.tribute.menu.scrollHeight;
                }
                previewTributeEventCallbackUpDown();
            }
        },
        down: (e, el) => {
            // navigate down ul
            if (this.tribute.isActive && this.tribute.current.filteredItems) {
                e.preventDefault();
                e.stopPropagation();
                let count = this.tribute.current.filteredItems.length - 1,
                    selected = this.tribute.menuSelected;

                if (count > selected) {
                    this.tribute.menuSelected++;
                    this.setActiveLi();
                } else if (count === selected) {
                    this.tribute.menuSelected = 0;
                    this.setActiveLi();
                    this.tribute.menu.scrollTop = 0;
                }
                previewTributeEventCallbackUpDown();
            }
        },
    };
};

tribute.attach(textInput);

const observer = new MutationObserver(function (mutations) {

    mutations.forEach(function (mutation) {
        if (mutation.type === 'attributes') {
            if (mutation.target.id === 'bricks-panel-element' && mutation.attributeName === 'style') {
                if (mutation.target.style.display !== 'none') {
                    visibleElementPanel.value = true;
                } else {
                    visibleElementPanel.value = false;
                }
            } else if ('placeholder' === mutation.attributeName && 'INPUT' === mutation.target.tagName && mutation.target.classList.contains('placeholder')) {
                activeElementId.value = brxGlobalProp.$_activeElement.value.id;
            }
        } else if (mutation.type === 'childList') {
            if (mutation.addedNodes.length > 0) {

                if (mutation.target.id === 'bricks-panel-sticky' && mutation.addedNodes[0].id === 'bricks-panel-element-classes') {
                    activeElementId.value = brxGlobalProp.$_activeElement.value.id;
                } else if (mutation.target.dataset && mutation.target.dataset.controlkey === '_cssClasses' && mutation.addedNodes[0].childNodes.length > 0) {
                    document.querySelector('#_cssClasses').addEventListener('input', function (e) {
                        nextTick(() => {
                            textInput.value = e.target.value;
                            onTextInputChanges();
                        });
                    });
                }
            }
        }
    });
});

observer.observe(document.getElementById('bricks-panel-element'), {
    subtree: true,
    attributes: true,
    childList: true,
});

watch([activeElementId, visibleElementPanel], (newVal, oldVal) => {
    if (newVal[0] !== oldVal[0]) {
        nextTick(() => {
            textInput.value = brxGlobalProp.$_activeElement.value.settings._cssClasses || '';
            onTextInputChanges();
        });
    }

    if (newVal[0] && newVal[1]) {
        nextTick(() => {
            const panelElementClassesEl = document.querySelector('#bricks-panel-element-classes');
            if (panelElementClassesEl.querySelector('.bricksbender-plc-input') === null) {
                panelElementClassesEl.appendChild(textInput);
                hit = new HighlightInTextarea(textInput, {
                    highlight: [
                        {
                            highlight: /(?<=\s|^)(?:(?!\s).)+(?=\s|$)/g,
                            className: 'word',
                        },
                        {
                            highlight: /(?<=\s)\s/g,
                            className: 'multispace',
                            blank: true,
                        },
                    ],
                });

            }
        });
    }
});

textInput.addEventListener('input', function (e) {
    brxGlobalProp.$_activeElement.value.settings._cssClasses = e.target.value;
});

function onTextInputChanges() {
    nextTick(() => {
        try {
            hit.handleInput();
        } catch (error) { }
        autosize.update(textInput);
        // tribute.setMenuContainer(document.querySelector('div.hit-container'));
        tribute.hideMenu();
    });
};

textInput.addEventListener('highlights-updated', function (e) {
    colorizeBackground();

});

function colorizeBackground() {
    if (twConfig === null) return;

    if (screenBadgeColors.length === 0) return;

    const markElements = document.querySelectorAll('.hit-backdrop>.hit-highlights.hit-content>mark[class="word"]');

    markElements.forEach((markElement) => {
        // get the text content of the `mark` element
        const text = markElement.textContent;

        // loop through all screen badge colors
        screenBadgeColors.forEach((screenBadgeColor) => {
            // if the text content of the `mark` element contains the screen name
            if (text.includes(screenBadgeColor.screen + ':')) {
                const ruleVal = `color-mix(in srgb, ${screenBadgeColor.color} 20%, white 1%)`;
                markElement.style.backgroundColor = ruleVal;
                markElement.style.outlineColor = ruleVal;
            }
        });
    });
}

const observerAutocomplete = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
            mutation.addedNodes.forEach((node) => {
                const className = node.querySelector('.class-name').dataset.tributeClassName;

                node.addEventListener('mouseenter', (e) => {
                    previewAddClass(className);
                });

                node.addEventListener('mouseleave', (e) => {
                    previewResetClass();
                });

                node.addEventListener('click', (e) => {
                    previewResetClass();
                });
            });
        }
    });
});

let menuAutocompleteItemeEl = null;

textInput.addEventListener('tribute-active-true', function (e) {
    if (menuAutocompleteItemeEl === null) {
        menuAutocompleteItemeEl = document.querySelector('.bricksbender-tribute-container>ul');
    }
    nextTick(() => {
        if (menuAutocompleteItemeEl) {
            observerAutocomplete.observe(menuAutocompleteItemeEl, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
    });
});


function truncateText(text) {
    // character to find: non-alphanumeric characterr, `[`, `]`, `'`, `#`, `"` and space
    const regex = /[^a-zA-Z0-9\[\]'"#\s]/g;

    // find all
    const match = text.match(regex);

    // if match is not found, return the original text
    if (!match) return text;

    // find the last index of the match
    const lastIndex = text.lastIndexOf(match[match.length - 1]);

    // truncate the text
    return text.slice(0, lastIndex + 1);
}

textInput.addEventListener('mouseup', function (e) {
    let selectedText = textInput.value.substring(textInput.selectionStart, textInput.selectionEnd);

    let trimedText = selectedText.trim();

    // no selected text
    if (trimedText.length === 0) {
        return;
    }

    textInput.setSelectionRange(textInput.selectionStart, textInput.selectionStart + selectedText.trimEnd().length);

    // reselect the start text to the left before the first space. reselect the end text to the right after the before space
    let start = textInput.selectionStart;
    let end = textInput.selectionEnd;

    while (start > 0 && textInput.value[start - 1] !== ' ') {
        start--;
    }

    while (end < textInput.value.length && textInput.value[end] !== ' ') {
        end++;
    }

    textInput.setSelectionRange(start, end);
    selectedText = textInput.value.substring(textInput.selectionStart, textInput.selectionEnd);

    let trunctedText = truncateText(selectedText);

    textInput.setSelectionRange(end, end);

    tribute.current.element = textInput;
    tribute.current.collection = tribute.collection[0];
    tribute.current.mentionText = trunctedText;

    tribute.showMenuFor(textInput);
});

function previewAddClass(className) {
    const elementNode = brxIframeGlobalProp.$_getElementNode(brxIframeGlobalProp.$_activeElement.value);
    elementNode.classList.add(className);
}

function previewResetClass() {
    const activeEl = brxIframeGlobalProp.$_activeElement.value;
    const elementNode = brxIframeGlobalProp.$_getElementNode(activeEl);
    const elementClasses = brxIframeGlobalProp.$_getElementClasses(activeEl);
    elementNode.classList.value = elementClasses.join(' ');
}

function previewTributeEventCallbackUpDown() {
    let li = tribute.menu.querySelector('li.highlight>span.class-name');
    const activeEl = brxIframeGlobalProp.$_activeElement.value;
    const elementNode = brxIframeGlobalProp.$_getElementNode(activeEl);
    const elementClasses = brxIframeGlobalProp.$_getElementClasses(activeEl);
    elementNode.classList.value = elementClasses.join(' ') + ' ' + li.dataset.tributeClassName;
}

logger('Module loaded!', { module: 'plain-classes' });