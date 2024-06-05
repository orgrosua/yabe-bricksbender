<template>
    <div class="wrap">
        <h2 v-if="$route.name == 'modules.index'" class="inline-block mr:8">
            {{ __('Modules', 'yabe-bricksbender') }}
        </h2>
        <h2 v-else class="inline-block mr:8">
            <router-link :to="{ name: 'modules' }" class="text:none">{{ __('Modules', 'yabe-bricksbender') }}</router-link>
        </h2>
        <router-view></router-view>
        <div class="clear"></div>
    </div>
</template>
  
<script setup>
</script>