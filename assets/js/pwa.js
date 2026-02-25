if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js')
            .then(reg => console.log('Service Worker registrado con éxito'))
            .catch(err => console.error('Fallo al registrar Service Worker', err));
    });
}
