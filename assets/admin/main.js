import { __, _n, sprintf } from '@wordpress/i18n';
import { createApp } from 'vue';
import { createPinia } from 'pinia';

import './styles/app.scss';
import 'floating-vue/dist/style.css';
import './master.css.js';
import './font-awesome.js';
import InlineSvg from 'vue-inline-svg';
import { FontAwesomeIcon } from './font-awesome.js';
import FloatingVue from 'floating-vue';

import App from './App.vue';
import router from './router.js';

import vRipple from './directives/ripple/ripple.js';

const pinia = createPinia();
const app = createApp(App);

app.config.globalProperties.__ = __;
app.config.globalProperties._n = _n;

app
    .use(pinia)
    .use(router)
    .use(FloatingVue, {
        container: '#bricksbender-app',
    });

app
    .component('font-awesome-icon', FontAwesomeIcon)
    .component('inline-svg', InlineSvg)
    ;

app.directive('ripple', vRipple);

app.mount('#bricksbender-app');