import { Controller } from 'stimulus';
import TomSelect from "tom-select/dist/js/tom-select.complete.min";

export default class extends Controller {
    connect() {
        // this avoids initializing the same field twice (TomSelect shows an error otherwise)
        if (this.element.classList.contains('tomselected')) {
            return;
        }

        new TomSelect(this.element, {
            create: true
        });
    }
}
