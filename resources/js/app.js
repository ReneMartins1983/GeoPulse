import './bootstrap';
import 'leaflet/dist/leaflet.css';
import { createApp } from 'vue';
import Dashboard from './components/Dashboard.vue';

const el = document.getElementById('app');
if (el) {
    createApp(Dashboard).mount(el);
}
