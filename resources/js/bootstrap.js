import axios from 'axios';
import { onPageLoad } from './page-load';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const syncAxiosCsrf = () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
};

syncAxiosCsrf();
onPageLoad(syncAxiosCsrf);
