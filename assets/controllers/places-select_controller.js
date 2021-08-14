import { Controller } from 'stimulus';
import TomSelect from "tom-select/dist/js/tom-select.complete.min";
import { useDispatch } from 'stimulus-use';

export default class extends Controller {
    connect() {
        useDispatch(this);

        // this avoids initializing the same field twice (TomSelect shows an error otherwise)
        if (this.element.classList.contains('tomselected')) {
            return;
        }

        this.element.control = new TomSelect(this.element);
        this.dispatch('added', {
            element: this.element,
        });
    }

    disconnect () {
        this.element.control.destroy();
    }
}
