<template>
    <transition mode="out-in">
        <tr :class="[item.status ? 'active' : 'inactive']" class="group">
            <th scope="row" :class="{ 'pl:6': !item.status }" class="vertical:middle py:8 ybb-check-column">
                <input v-model="selectedItems" type="checkbox" :value="item.id" :disabled="busy.isBusy" />
            </th>
            <td width="1%" class="manage-column vertical:middle">
                <Switch :aria-disabled="busy.isBusy" :checked="item.status" @click="$emit('updateStatus')" @keyup="handleKeyUp" :class="[item.status ? 'bg:sky-60' : 'opacity:.5 bg:gray-20']" class="rel inline-flex p:0 h:24 w:44 flex-shrink:0 cursor:pointer rounded b:2 b:transparent transition-property:color,background-color,border-color,text-decoration-color,fill,stroke transition-duration:200 transition-timing-function:cubic-bezier(0.4,0,0.2,1) box-shadow:rgb(255,255,255)|0|0|0|2,rgb(14,165,233)|0|0|0|4,rgba(0,0,0,0)|0|0|0|0:focus outline:2|solid|transparent:focus">
                    <span :class="[item.status ? 'translateX(20)' : 'translateX(0)']" class="pointer-events:none rel inline-block font:12 h:20 w:20 rounded bg:white transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter transition-duration:200 transition-timing-function:cubic-bezier(0.4,0,0.2,1) box-shadow:rgb(255,255,255)|0|0|0|0,rgba(59,130,246,0.5)|0|0|0|0,rgba(0,0,0,0.1)|0|1|3|0,rgba(0,0,0,0.1)|0|1|2|-1">
                        <span aria-hidden="true" :class="[item.status ? 'opacity:0 transition-timing-function:ease-out transition-duration:100' : 'opacity:1 transition-timing-function:ease-in transition-duration:200']" class="abs inset:0 flex h:full w:full align-items:center justify-content:center tw-transition-opacity">
                            <font-awesome-icon v-if="!item.isUpdatingStatus" :icon="['fas', 'xmark']" class="fg:gray-60" />
                            <font-awesome-icon v-else :icon="['fas', 'spinner']" class="animation:rotate|linear|1s|infinite fg:gray-60" />
                        </span>
                        <span aria-hidden="true" :class="[item.status ? 'opacity:1 transition-timing-function:ease-in transition-duration:200' : 'opacity:0 transition-timing-function:ease-out transition-duration:100']" class="abs inset:0 flex h:full w:full align-items:center justify-content:center tw-transition-opacity">
                            <font-awesome-icon v-if="!item.isUpdatingStatus" :icon="['fas', 'check']" class="fg:sky-60" />
                            <font-awesome-icon v-else :icon="['fas', 'spinner']" class="animation:rotate|linear|1s|infinite fg:sky-60" />
                        </span>
                    </span>
                </Switch>
            </td>
            <td width="30%" class="vertical:middle rel">
                <div class="flex align-items:center" :title="item.id">
                    <div v-if="!item.icon.hasOwnProperty('kind') || item.icon.kind === 'font-awesome'">
                        <font-awesome-icon :icon="item.icon.icon" :class="item.icon.class ?? {}" class="flex align-items:center mr:6 font:20" />
                    </div>
                    <template v-if="item.status && item.hasOwnProperty('hasSettingPage') && item.hasSettingPage">
                        <router-link :to="{ name: `modules.m.${item.id}` }" :class="{
                            'font:semibold': item.status
                        }">
                            {{ item.title }}
                        </router-link>
                    </template>
                    <template v-else>
                        {{ item.title }}
                    </template>
                </div>
            </td>
            <td width="10%" class="vertical:middle">
                {{ item.version }}
            </td>
            <td width="" class="vertical:middle">
                {{ item.description }}
            </td>
        </tr>
    </transition>
</template>

<script setup>
import { inject } from 'vue';
import { useBusy } from '../../stores/busy';

import { Switch } from '@headlessui/vue';

const busy = useBusy();

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['updateStatus']);

const selectedItems = inject('selectedItems');

function handleKeyUp(e) {
    if (e.code === 'Space') {
        e.preventDefault();
        emit('updateStatus');
    }
}
</script>

<style scoped>
.v-leave-active {
    transition: all 0.35s;
}

.v-leave-to,
.v-leave-to td,
.v-leave-to th {
    background-color: #faafaa !important;
}
</style>