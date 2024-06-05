<template>
    <div class="alignleft actions bulkactions">
        <select id="bulk-action-selector-top" v-model="model" name="action" :disabled="busy.isBusy">
            <option value="-1">
                {{ __('Bulk actions', 'yabe-bricksbender') }}
            </option>
            <option v-for="action in actions" :key="action.key" :value="action.key">
                {{ action.label }}
            </option>
        </select>
        <button type="submit" class="button action" :disabled="busy.isBusy" @click="$emit('bulkActions')" v-ripple >
            {{ __('Apply', 'yabe-bricksbender') }}
        </button>
    </div>
</template>

<script setup>
import { useBusy } from '../stores/busy.js';

const props = defineProps({
    actions: {
        type: Array,
        default: () => []
    },
});

const busy = useBusy();

const model = defineModel();

const emit = defineEmits(['bulkActions']);
</script>