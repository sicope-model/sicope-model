import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ 'placesSelect' ]
    static values = { places: Array }

    connect() {
        this.placesValue = [];
    }

    disconnect () {
        delete this.placesValue;
    }

    removeOption(event) {
        const index = this.placesValue.findIndex(place => place.value === event.detail.value);
        this.placesValue = [
            ...this.placesValue.slice(0, index),
            ...this.placesValue.slice(index + 1)
        ];
    }

    addOption(event) {
        this.placesValue = [
            ...this.placesValue,
            {value: event.detail.value, text: event.detail.text}
        ];
    }

    updateOption(event) {
        const index = this.placesValue.findIndex(place => place.value === event.detail.value);
        this.placesValue = [
            ...this.placesValue.slice(0, index),
            {value: event.detail.value, text: event.detail.text},
            ...this.placesValue.slice(index + 1)
        ];
    }

    setOptions(event) {
        this.#reloadOptions(event.detail.element);
    }

    placesValueChanged() {
        this.placesSelectTargets.forEach(this.#reloadOptions.bind(this));
    }

    #reloadOptions(select) {
        if (select.control) {
            const values = select.value.split(',');
            select.control.clear();
            select.control.clearOptions();
            this.placesValue.forEach(function (option) {
                select.control.addOption(option);
            })
            select.control.refreshOptions(false);
            values.forEach(function (value) {
                select.control.addItem(value);
            });
            select.control.refreshItems();
        }
    }
}

