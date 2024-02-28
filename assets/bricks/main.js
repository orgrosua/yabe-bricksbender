
console.log('Bricksbender is loading...');

while (!document.querySelector('.brx-body')?.__vue_app__) {
    await new Promise(resolve => setTimeout(resolve, 100));
}

while (!document.getElementById('bricks-builder-iframe')?.contentDocument.querySelector('.brx-body')?.__vue_app__) {
    await new Promise(resolve => setTimeout(resolve, 100));
}

console.log('Hello from Bricksbender!');

console.log('Loading modules...');

// TODO: dynamic import the features based on the enabled modules
await import('./modules/plain-classses/plain-classes.js');

console.log('Modules loaded!');