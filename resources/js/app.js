require('./bootstrap');
require('./main')

import Alpine from 'alpinejs'

window.Alpine = Alpine

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start()
})
