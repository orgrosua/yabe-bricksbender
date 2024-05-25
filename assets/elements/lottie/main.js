function ybrLottie() {
    bricksQuerySelectorAll(document, '.brxe-ybr_lottie').forEach(function (lottieEl) {
        const settings = JSON.parse(lottieEl.dataset.ybrLottieSettings);

        if (settings?.trigger === 'click') {
            lottieEl.addEventListener('click', () => lottieEl.dotLottie.play());
        } else if (settings?.trigger === 'hover') {
            const currMode = settings?.mode || 'forward';
            let lastFrame = lottieEl.dotLottie.currentFrame;

            lottieEl.addEventListener('mouseenter', () => {
                lastFrame = lottieEl.dotLottie.currentFrame;
                lottieEl.dotLottie.setMode(currMode);
                lottieEl.dotLottie.setFrame(lastFrame);
                lottieEl.dotLottie.play();
            });

            if (settings?.hover_out && settings.hover_out !== 'noaction') {
                if (settings.hover_out === 'pause') {
                    lottieEl.addEventListener('mouseleave', () => lottieEl.dotLottie.pause());
                } else if (settings.hover_out === 'stop') {
                    lottieEl.addEventListener('mouseleave', () => lottieEl.dotLottie.stop());
                } else if (settings.hover_out === 'reverse') {
                    lottieEl.addEventListener('mouseleave', () => {
                        lastFrame = lottieEl.dotLottie.currentFrame;
                        lottieEl.dotLottie.setMode(!currMode.includes('reverse') ? 'reverse' : 'forward');
                        lottieEl.dotLottie.setFrame(lastFrame);
                        lottieEl.dotLottie.play();
                    });
                }
            }
        } else if (settings?.trigger === 'scroll') {
            // TODO: implement the trigger relative to an element
            window.addEventListener('scroll', () => {
                // get the scroll position
                const scrollPosition = window.scrollY;

                // get the total height of the document
                const totalHeight = document.body.scrollHeight;

                // get the height of the viewport
                const viewportHeight = window.innerHeight;

                // calculate the percentage of the scroll
                const scrollPercentage = (scrollPosition / (totalHeight - viewportHeight)) * 100;

                // calculate the frame number
                const targetFrame = (lottieEl.dotLottie.totalFrames / 100) * scrollPercentage;

                lottieEl.dotLottie.setFrame(Math.round(targetFrame));
            });
        } else if (settings?.trigger === 'viewport') {
            // if the element is visible in the viewport
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (lottieEl.dotLottie.isPaused || lottieEl.dotLottie.isStopped) {
                            lottieEl.dotLottie.play();
                        }
                    } else {
                        if (lottieEl.dotLottie.isPlaying) {
                            lottieEl.dotLottie.pause();
                        }
                    }
                });
            });

            observer.observe(lottieEl);
        }
    });
}

// expose the function to the global scope
window.ybrLottie = ybrLottie;

// run the function
document.addEventListener('DOMContentLoaded', ybrLottie);