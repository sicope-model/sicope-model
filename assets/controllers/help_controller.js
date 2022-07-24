import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        texts: Object
    }

    connect() {
        this.element.addEventListener('change', this.#onCommandChange.bind(this));
        this.element.dispatchEvent(new Event('change'));
    }

    #onCommandChange(event) {
        const command = event.target.value;
        const accordionBody = event.target.closest('.accordion-body');
        accordionBody.querySelector('.value-help').innerText = this.textsValue[command]['value'];
        accordionBody.querySelector('.target-help').innerText = this.textsValue[command]['target'];
    }
}
