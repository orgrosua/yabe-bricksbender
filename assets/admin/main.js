import { createApp } from 'vue';

import './styles/app.scss';
import 'floating-vue/dist/style.css';
import './master.css.js';
import './font-awesome.js';
import InlineSvg from 'vue-inline-svg';
import { FontAwesomeIcon } from './font-awesome.js';
import FloatingVue from 'floating-vue';

import App from './App.vue';

const app = createApp(App);

app
    .use(FloatingVue, {
        container: '#bricksbender-app',
    });

app
    .component('font-awesome-icon', FontAwesomeIcon)
    .component('inline-svg', InlineSvg)
;

app.mount('#bricksbender-app');