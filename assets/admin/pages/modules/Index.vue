<script setup>
import { ref, computed, onMounted, provide } from 'vue';
import { __ } from '@wordpress/i18n';

import { useApi } from '../../library/api';
import { useBusy } from '../../stores/busy';

import TheBulkAction from '../../components/TheBulkAction.vue';
import TheModuleIndexRow from '../../components/modules/TheModuleIndexRow.vue';
import { useNotifier } from '../../library/notifier';
// import { useWordpressNotice } from '../../stores/wordpressNotice';
import { Switch } from '@headlessui/vue';

const api = useApi();
const busy = useBusy();
// const wordpressNotice = useWordpressNotice();

const notifier = useNotifier();

const items = ref([]);

const chosenBulkAction = ref('-1');
const selectedItems = ref([]);
provide('selectedItems', selectedItems);

const getBusyHasTask = busy.hasTask;

onMounted(() => {
    busy.reset();
    doRefreshItems();
});

function doRefreshItems() {
    busy.add('modules.index:fetch-items');
    api
        .request({
            method: 'GET',
            url: '/modules/index',
        })
        .then(response => response.data)
        .then(data => {
            items.value = data.data.modules.map(item => {
                item.isUpdatingStatus = false;
                return item;
            });

            resetBulkSelection();
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('modules.index:fetch-items');
            resetBulkSelection();
        });
}

function doUpdateStatus(item, status = null) {
    if (status === item.status) {
        return;
    }

    busy.add('modules.index:update-status');
    item.isUpdatingStatus = true;

    api
        .request({
            method: 'PATCH',
            url: `/modules/update-status/${item.id}`,
            data: {
                status: status !== null ? status : !item.status,
            },
        })
        .then((response) => {
            return response.data;
        })
        .then(data => {
            item.status = data.status;
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            item.isUpdatingStatus = false;
            busy.remove('modules.index:update-status');
        });
}

const selectAll = computed({
    get() {
        if (items.value.length > 0) {
            let allChecked = true;
            for (const [index, item] of items.value.entries()) {
                if (!selectedItems.value.includes(item.id)) {
                    allChecked = false;
                }
                if (!allChecked) break;
            }
            return allChecked;
        }
        return false;
    },
    set(value) {
        const checked = [];
        if (value) {
            items.value.forEach((item) => {
                checked.push(item.id);
            });
        }
        selectedItems.value = checked;
    },
});

function resetBulkSelection() {
    selectedItems.value = [];
}

const bulkActions = ref([
    { key: 'activate', label: 'Activate' },
    { key: 'deactivate', label: 'Deactivate' },
]);

function doBulkActions() {
    if (chosenBulkAction.value === '-1') {
        return;
    }
    switch (chosenBulkAction.value) {
        case 'deactivate':
            if (
                confirm(__(`Are you sure you want to deactivate the selected module(s)?`, 'yabe-bricksbender'))
            ) {
                selectedItems.value.forEach(async (id) => {
                    const item = items.value.find((item) => item.id === id);
                    doUpdateStatus(item, false);
                });
                resetBulkSelection();
            }
            break;
        case 'activate':
            if (
                confirm(__(`Are you sure you want to activate the selected module(s)?`, 'yabe-bricksbender'))
            ) {
                selectedItems.value.forEach(async (id) => {
                    const item = items.value.find((item) => item.id === id);
                    doUpdateStatus(item, true);
                });
                resetBulkSelection();
            }
            break;
        default:
            break;
    }
}
</script>

<template>
    <button type="button" :disabled="busy.isBusy" @click="doRefreshItems" v-ripple class="button float:right"> refresh 🔄️</button>

    <hr class="invisible m:0 mt:-2" />

    <div class="flex flex:col align-items:center {w:full;max-w:screen-sm}>*">
        <div class="tablenav top">
            <TheBulkAction v-model="chosenBulkAction" :actions="bulkActions" @bulk-actions="doBulkActions" />
            <div class="tablenav-pages pb:12">
                <span class="displaying-num"> {{ `${items.length} ${__('items', 'yabe-bricksbender')}` }} </span>
            </div>
            <br class="clear" />
        </div>

        <table class="wp-list-table widefat table-auto plugins">
            <thead>
                <tr>
                    <td class="manage-column column-cb ybb-check-column px:2 vertical:middle">
                        <input v-model="selectAll" class="ml:12" type="checkbox" />
                    </td>
                    <td class="manage-column"> </td>
                    <th scope="col">
                        {{ __('Title', 'yabe-bricksbender') }}
                    </th>
                    <th scope="col">
                        {{ __('Version', 'yabe-bricksbender') }}
                    </th>
                    <th scope="col">
                        {{ __('Description', 'yabe-bricksbender') }}
                    </th>

                </tr>
            </thead>
            <tbody v-if="items.length > 0 && !getBusyHasTask('modules.index:fetch-items')">
                <TheModuleIndexRow v-for="item in items" :key="item.id" :item="item" @update-status="doUpdateStatus(item, null)" />
            </tbody>
            <tbody v-else-if="getBusyHasTask('modules.index:fetch-items')">
                <tr v-for="skeleton in items.length ? items.length : 5" class="inactive animation:skeleton|2s|infinite">
                    <th scope="row" class="vertical:middle py:8 ybb-check-column">
                        <input type="checkbox" value="0" disabled />
                    </th>
                    <td width="1%" class="manage-column vertical:middle">
                        <Switch :checked="false" class="opacity:.5 bg:gray-85 rel inline-flex p:0 h:24 w:44 flex-shrink:0 cursor:pointer rounded b:2 b:transparent">
                            <span class="translateX(0) pointer-events:none rel inline-block h:20 w:20 rounded bg:white box-shadow:0|0">
                                <span aria-hidden="true" class="abs inset:0 flex h:full w:full align-items:center justify-content:center opacity:1">
                                    <font-awesome-icon :icon="['fas', 'spinner']" class="animation:rotate|linear|1s|infinite font:12 fg:gray-60" />
                                </span>
                            </span>
                        </Switch>
                    </td>
                    <td width="30%">
                        <div class="h:12 bg:slate-30 r:4 w:1/2"></div>
                    </td>
                    <td width="10%" class="align-items:center vertical:middle">
                        <div class="h:12 bg:slate-30 r:4 w:1/2"></div>
                    </td>
                    <td width="" class="align-items:center vertical:middle">
                        <div class="h:12 bg:slate-30 r:4 w:full"></div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else></tbody>
            <tfoot>
                <tr>
                    <td class="manage-column column-cb ybb-check-column px:2 vertical:middle">
                        <input v-model="selectAll" class="ml:12" type="checkbox" />
                    </td>
                    <td class="manage-column"></td>
                    <th scope="col">
                        {{ __('Title', 'yabe-bricksbender') }}
                    </th>
                    <th scope="col">
                        {{ __('Version', 'yabe-bricksbender') }}
                    </th>
                    <th scope="col">
                        {{ __('Description', 'yabe-bricksbender') }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="tablenav bottom">
            <TheBulkAction v-model="chosenBulkAction" :actions="bulkActions" @bulk-actions="doBulkActions" />
            <div class="tablenav-pages">
                <span class="displaying-num"> {{ `${items.length} ${__('items', 'yabe-bricksbender')}` }} </span>
            </div>
            <br class="clear" />
        </div>
    </div>
</template>

<style>
.widefat .ybb-check-column {
    width: 2.2em;
    padding: 6px 0 25px;
    vertical-align: top
}

.widefat tbody th.ybb-check-column {
    padding: 9px 0 22px
}

.updates-table tbody td.ybb-check-column,
.widefat tbody th.ybb-check-column,
.widefat tfoot td.ybb-check-column,
.widefat thead td.ybb-check-column {
    padding: 11px 0 0 3px
}

.widefat tfoot td.ybb-check-column,
.widefat thead td.ybb-check-column {
    padding-top: 4px;
    vertical-align: middle
}

.plugins tbody,
.plugins tbody th.ybb-check-column {
    padding: 8px 0 0 2px
}

.plugins tbody th.ybb-check-column input[type=checkbox] {
    margin-top: 4px
}

.plugins .inactive th.ybb-check-column,
.plugins tfoot td.ybb-check-column,
.plugins thead td.ybb-check-column {
    padding-left: 6px
}

.plugin-update-tr.active td,
.plugins .active th.ybb-check-column {
    border-left: 4px solid #72aee6
}

.plugins tr.paused th.ybb-check-column {
    border-left: 4px solid #b32d2e
}

@media screen and (max-width: 782px) {
    .wp-list-table tr th.ybb-check-column {
        display: table-cell
    }

    .wp-list-table .ybb-check-column {
        width: 2.5em
    }

    .wp-list-table tr:not(.inline-edit-row):not(.no-items) td:not(.ybb-check-column) {
        position: relative;
        clear: both;
        width: auto !important
    }

    .wp-list-table tr:not(.inline-edit-row):not(.no-items) td.column-primary~td:not(.ybb-check-column) {
        padding: 3px 8px 3px 35%
    }

    .widefat tfoot td.ybb-check-column,
    .widefat thead td.ybb-check-column {
        padding-top: 10px
    }

    .plugins .plugin-update-tr:before,
    .plugins tr.active+tr.inactive td.column-description,
    .plugins tr.active+tr.inactive th.ybb-check-column {
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .1)
    }

    .plugins tr.active+tr.inactive td,
    .plugins tr.active+tr.inactive th.ybb-check-column {
        border-top: none
    }

    .plugins tbody th.ybb-check-column {
        padding: 8px 0 0 5px
    }

    .plugins .inactive th.ybb-check-column,
    .plugins tfoot td.ybb-check-column,
    .plugins thead td.ybb-check-column {
        padding-left: 9px
    }
}

.plugins tr:last-child.active td,
.plugins tr:last-child.active th,
.plugins tr:last-child.inactive td,
.plugins tr:last-child.inactive th {
    box-shadow: none;
}
</style>