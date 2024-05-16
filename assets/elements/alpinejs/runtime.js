function ybrAlpinejsRuntime() {
    bricksQuerySelectorAll(document, '.brxe-ybr_alpinejs_runtime').forEach(function (element) {
        const runtimeAssets = JSON.parse(element.dataset.ybrAlpinejsRuntimeOptions).assets;

        runtimeAssets.forEach(function (asset) {
            const existScript = document.getElementById(asset.handle)

            if (existScript) {
                if (existScript.id !== 'ybr-alpinejs-core' && existScript.dataset.version === asset.version) {
                    return;
                }

                existScript.remove();
            }

            const script = document.createElement('script');
            script.id = asset.handle;
            script.src = asset.src;
            script.defer = true;
            script.dataset.version = asset.version;

            document.head.appendChild(script);
        });
    });
}

// expose ybrAlpinejsRuntime to the global scope
window.ybrAlpinejsRuntime = ybrAlpinejsRuntime;