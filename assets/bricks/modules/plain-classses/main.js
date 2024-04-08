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
import { brxGlobalProp, brxIframeGlobalProp } from '../../constant.js';

const textInput = document.createElement('textarea');
textInput.classList.add('bricksbender-plc-input');
textInput.setAttribute('rows', '3');
textInput.setAttribute('spellcheck', 'false');

const visibleElementPanel = ref(false);
const activeElementId = ref(null);

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