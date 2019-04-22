import router from './routes.js'

require('./bootstrap');

window.Vue = require('vue');

Vue.component('pixels', require('./components/PixelBoard.vue'));

window.app = new Vue({
    el: '#app',
    router,
    data: {
    },
    methods: {
        swag() {
            this.message = 'SWAAAG!';
        }
    },
});
