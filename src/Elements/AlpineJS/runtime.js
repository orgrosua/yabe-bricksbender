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

    logger('Loading Alpine.js Runtime...');

    
    ybrAlpinejsRuntime();

    logger('Alpine.js Runtime loaded!');

    // console.log('document', document);

    // <script type="text/x-template" id="tmpl-bricks-element-animated-typing"></script>

    // get all script with type="text/x-template" and id="tmpl-bricks-element-*"
    // const xTemplates = document.getElementById('bricks-builder-iframe')?.contentDocument.querySelectorAll('script[type="text/x-template"][id^="tmpl-bricks-element-"]');
    // xTemplates.forEach(element => {
    //     console.log(element);
        
    //     // v-bind="{
    //     //     ...(settings._attributes ? Object.assign({}, ...settings._attributes.map(attr => ({ [attr.name]: attr.value }))) : {})
    //     // }"

    //     if (element.id === 'tmpl-bricks-element-text-basic') {
    //         element.innerHTML = /* html */ `
	// 		<contenteditable
    //             :key="tag"
    //             :name="name"
    //             controlKey="text"
    //             toolbar="style align"
    //             lineBreak="br"
    //             :breeewww="JSON.stringify(settings)"
    //             v-bind="{
    //                 ...(settings._attributes ? Object.assign({}, ...settings._attributes.map(attr => ({ [attr.name]: attr.value }))) : {})
    //             }"
    //             :settings="settings"/>
    //         `;

    //         console.log('element', element);
    //     }

    //     console.log('id', element.id);

    // });

    // console.log('xTemplates', xTemplates);

})();

function logger(message) {
    console.log(`[YABE BricksBender] ${message}`);
}