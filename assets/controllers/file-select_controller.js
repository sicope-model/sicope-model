import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select/dist/js/tom-select.complete.min";

export default class extends Controller {
    connect() {
        this.element.addEventListener('change', this.#onCommandChange);
        this.element.dispatchEvent(new Event('change'));
    }

    #onCommandChange(event) {
        const value = event.target.closest('.accordion-body').querySelector('.command-value');
        const control = value.tomselect;
        if (event.target.value === 'upload') {
            if (!control) {
                new TomSelect(value, {
                    valueField: 'path',
                    labelField: 'path',
                    searchField: 'path',
                    maxItems: 1,
                    // fetch remote data
                    load: function(query, callback) {
                        var self = this;
                        if( self.loading > 1 ){
                            callback();
                            return;
                        }
            
                        var url = '/file-suggestions';
                        fetch(url)
                            .then(response => response.json())
                            .then(json => {
                                callback(json.files);
                                self.settings.load = null;
                            }).catch(()=>{
                                callback();
                            });
            
                    },
                });
            }
        } else {
            if (control) {
                control.destroy();
            }
        }
    }
}
