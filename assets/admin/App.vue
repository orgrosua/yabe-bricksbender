<script setup>
import Logo from '../../bricksbender.svg';
</script>

<template>
    <div>
        <div class="bricksbender-main rel">
            <header id="bricksbender-header" class="flex sticky align-items:center bg:white px:20 py:6 top:$(wp-admin--admin-bar--height) z:12">
                <div class="flex align-items:center fg:black!_* flex-grow:1 gap:10">
                    <inline-svg :src="Logo" class="inline-svg f:40 fill:current px:2" />
                    <h1 class="">Yabe Bricksbender</h1>
                </div>
                <div class="">
                    <div class="flex align-items:center flex:row gap:10">
                        <VDropdown :distance="12">
                            <button class="button button-secondary b:0 bg:transparent bg:gray-10:hover fg:gray-90 h:36 min-w:36 my:auto width:auto">
                                <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" class="font:15" />
                            </button>

                            <template #popper>
                                <div>
                                    <div role="group" class="flex flex:column font:14 min-w:120 p:4 w:auto">
                                        <a href="https://bricksbender.yabe.land/docs?utm_source=wordpress-plugins&utm_medium=plugin-menu&utm_campaign=yabe-bricksbender&utm_id=pro-version" target="_blank" class="flex align-items:center bg:white bg:gray-10:hover box-shadow:none:focus cursor:pointer fg:gray-90 gap:10 px:10 py:6 r:4 text:none user-select:none">
                                            <font-awesome-icon :icon="['fas', 'book']" class="min-w:14" />
                                            Documentation
                                        </a>
                                        <a href="https://rosua.org/support-portal" target="_blank" class="flex align-items:center bg:white bg:gray-10:hover box-shadow:none:focus cursor:pointer fg:gray-90 gap:10 px:10 py:6 r:4 text:none user-select:none">
                                            <font-awesome-icon :icon="['fas', 'user-headset']" class="min-w:14" />
                                            Support
                                        </a>
                                        <a href="https://www.facebook.com/groups/1142662969627943" target="_blank" class="flex align-items:center bg:white bg:gray-10:hover box-shadow:none:focus cursor:pointer fg:gray-90 gap:10 px:10 py:6 r:4 text:none user-select:none">
                                            <font-awesome-icon :icon="['fab', 'facebook']" class="min-w:14" />
                                            Community
                                        </a>
                                        <!-- changelog -->
                                    </div>
                                </div>
                            </template>
                        </VDropdown>
                    </div>
                </div>
            </header>

            <div class="mx:auto p:0">
                <div class="bb:1|solid|gray-20 bg:gray-5 mb:20">
                    <div class="flex flex:row mx:30">
                        <ul class="flex uppercase {bb:3|solid|black}>li:has(>.router-link-active) {fg:black}>li:has(>.router-link-active)>a align-items:baseline box-shadow:none>li>a:focus fg:gray-70>li>a fg:gray-90>li>a:hover flex-grow:1 font:12 font:semibold gap-x:28 m:0 m:0>li pb:6>li pt:20 pt:10>li px:4>li text-decoration:none>li>a">
                            <li><a href="#/settings" class="router-link-active router-link-exact-active" aria-current="page">Settings</a></li>
                        </ul>
                        <div id="navbar-right-side"></div>
                    </div>
                </div>

                <div class="bricksbender-notice-pool b:0 mx:0">
                    <hr class="wp-header-end">
                    <!-- <WordpressNotice /> -->
                </div>

                <div class="bricksbender-content my:20 px:20">
                    <h2>The dashboard will be available in future updates.</h2>
                    <br>
                    <h3> Resources </h3>
                    <ul>
                        <li><a href="https://yabe.land" target="_blank">Ecosystem plugins</a></li>
                        <li><a href="https://www.facebook.com/groups/1142662969627943" target="_blank">Community</a></li>
                        <li><a href="https://github.com/orgrosua/yabe-bricksbender/issues" target="_blank">Feature request / Report a bug</a></li>
                        <li><a href="https://github.com/orgrosua/yabe-bricksbender/discussions" target="_blank">Discussion</a></li>
                        <li><a href="https://ko-fi.com/Q5Q75XSF7" target="_blank">Support plugin development</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss"></style>