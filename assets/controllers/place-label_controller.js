import { Controller } from 'stimulus';
import { useDispatch } from 'stimulus-use';

export default class extends Controller {
    id;

    connect() {
        useDispatch(this);

        this.id = this.element.name.match(/\[(\d+)\]/)[1];
        this.dispatch('added', {
            value: this.id,
            text: this.element.value,
        });
        const self = this;
        this.element.addEventListener('change', function (e) {
            self.dispatch('updated', {
                value: self.id,
                text: e.target.value
            });
        });
    }

    disconnect () {
        window.dispatchEvent( new CustomEvent('place-label:removed', { detail: { value: this.id }}) );
        /*this.dispatch('removed', {
            value: this.id,
        });*/
    }
}
