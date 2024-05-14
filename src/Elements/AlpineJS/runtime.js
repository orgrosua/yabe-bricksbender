function ybrAlpinejsRuntime() {
    bricksQuerySelectorAll(document, '.brxe-ybr_alpinejs_runtime').forEach(function (element) {
        const runtimeAssets = JSON.parse(element.dataset.ybrAlpinejsRuntimeOptions).assets;

        runtimeAssets.forEach(function (asset) {
            const existScript = document.getElementById(asset.handle)

            if (existScript) {
                if (existScript.dataset.version === asset.version) {
                    return;
                }

                existScript.remove();
            }

            const script = document.createElement('script');
            script.id = asset.handle;
            script.src = asset.src;
            script.defer = true;
            script.dataset.version = asset.version;
            script.addEventListener('load', function () {});

            document.head.appendChild(script);
        });
    });
}

(async () => {
    while (!document.querySelector('.brx-body')?.__vue_app__) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    while (!document.getElementById('bricks-builder-iframe')?.contentDocument.querySelector('.brx-body')?.__vue_app__) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    ybrAlpinejsRuntime();
})();