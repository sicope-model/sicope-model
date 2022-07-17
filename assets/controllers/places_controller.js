import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select/dist/js/tom-select.complete.min";

export default class extends Controller {
    static targets = [ 'placesSelect' ]
    static values = {
        places: Array,
        lastDeletedPlaceIndex: Number
    }

    connect() {
        this.placesValue = [];
        this.lastDeletedPlaceIndex = null;
    }

    disconnect () {
        delete this.placesValue;
        delete this.lastDeletedPlaceIndex;
    }

    deleteItem(event) {
        const label = event.detail.delete_elem.querySelector('.place-label');
        if (label) {
            return this.#deletePlaceLabel(event.detail.index);
        }
        const selects = event.detail.delete_elem.querySelectorAll('.places-select');
        if (selects) {
            return selects.forEach(this.#deletePlacesSelect.bind(this));
        }
    }

    addItem(event) {
        const label = event.detail.new_elem.querySelector('.place-label');
        if (label) {
            return this.#addPlaceLabel(label, event.detail.index);
        }
        const selects = event.detail.new_elem.querySelectorAll('.places-select');
        if (selects.length > 0) {
            return selects.forEach(this.#addPlacesSelect.bind(this));
        }
    }

    placesValueChanged() {
        this.placesSelectTargets.forEach(this.#reloadOptions.bind(this));
    }

    #deletePlaceLabel(index) {
        this.lastDeletedPlaceIndex = index;
        this.placesValue = [
            ...this.placesValue.slice(0, index),
            ...this.placesValue.slice(index + 1).map(place => {
                return {...place, value: place.value - 1};
            })
        ];
        setTimeout(() => this.lastDeletedPlaceIndex = null);
    }

    #addPlaceLabel(label, index) {
        const _self = this;
        this.placesValue = [
            ...this.placesValue,
            {value: index, text: label.value}
        ];
        label.addEventListener('change', function (event) {
            _self.placesValue = [
                ..._self.placesValue.slice(0, index),
                {value: index, text: event.target.value},
                ..._self.placesValue.slice(index + 1)
            ];
        });
    }

    #addPlacesSelect(select) {
        // this avoids initializing the same field twice (TomSelect shows an error otherwise)
        if (select.classList.contains('tomselected')) {
            return;
        }

        select.control = new TomSelect(select, {
            plugins: ['clear_button', 'remove_button'],
        });

        this.#reloadOptions(select);
    }

    #deletePlacesSelect(select) {
        select.control.destroy();
    }

    #reloadOptions(select) {
        if (select.control) {
            let values = select.value.split(',');
            if (this.lastDeletedPlaceIndex) {
                values = values.map(value => value > this.lastDeletedPlaceIndex ? value - 1 : value);
            }
            select.control.clear(); // Must clear items before clearing options to avoid options caching
            select.control.clearOptions();
            this.placesValue.forEach(function (option) {
                select.control.addOption(option);
            })
            select.control.refreshOptions(false);
            values.forEach(value => select.control.addItem(value));
            select.control.refreshItems();
        }
    }
}
