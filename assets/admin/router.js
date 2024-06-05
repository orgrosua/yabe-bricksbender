import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(`${bricksbender.web_history}#/`),
    scrollBehavior(_, _2, savedPosition) {
        return savedPosition || { left: 0, top: 0 };
    },
    routes: [
        { path: '/', name: 'home', redirect: { name: 'modules' } },
        {
            path: '/modules',
            name: 'modules',
            component: () => import('./pages/modules/Base.vue'),
            redirect: { name: 'modules.index' },
            children: [
                {
                    path: 'index',
                    name: 'modules.index',
                    component: () => import('./pages/modules/Index.vue'),
                },
                {
                    'path': 'm',
                    'name': 'modules.m',
                    // 'component': () => import('./pages/modules/Module.vue'),
                    children: [
                        {
                            path: 'element_switch',
                            name: 'modules.m.element_switch',
                            component: () => import('./pages/modules/m/element-switch/Base.vue'),
                            redirect: { name: 'modules.m.element_switch.index' },
                            children: [
                                {
                                    path: 'index',
                                    name: 'modules.m.element_switch.index',
                                    component: () => import('./pages/modules/m/element-switch/Index.vue'),
                                },
                            ],
                        }
                    ],
                }
            ],
        },
        // {
        //     path: '/settings',
        //     name: 'settings',
        //     component: () => import('./pages/settings/Index.vue'),
        // },
        {
            path: '/:pathMatch(.*)*',
            name: 'NotFound',
            component: () => import('./pages/NotFound.vue'),
        },
    ]
});

export default router;