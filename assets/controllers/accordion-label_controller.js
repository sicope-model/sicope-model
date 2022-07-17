import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('change', this.#onChange);
        this.element.dispatchEvent(new Event('change'));
    }

    #onChange(event) {
        let label = event.target.value;
        if (event.target.tagName === 'SELECT') {
            label = event.target.options[event.target.selectedIndex].text;
        }
        event.target.closest('.accordion-item').querySelector('.accordion-header > .accordion-button > .collection-js-accordion-label').innerText = label;
    }
}
