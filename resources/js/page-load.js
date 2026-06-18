/** Fires on first paint and after every Turbo visit. */
export function onPageLoad(fn) {
    document.addEventListener('turbo:load', fn);

    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        fn();
    }
}
