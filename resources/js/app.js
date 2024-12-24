import './bootstrap';

import Alpine from 'alpinejs';
// If you are using JavaScript/ECMAScript modules:
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";

Dropzone.autoDiscover = false;
window.Dropzone = Dropzone;

window.Alpine = Alpine;

Alpine.start();
