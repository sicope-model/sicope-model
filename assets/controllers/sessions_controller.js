// mychart_controller.js

import { Controller } from 'stimulus';

const LIMIT = 6;

export default class extends Controller {
    static targets = [ 'total', 'used', 'queued', 'pending' ]
    #sessions = []

    connect() {
        this.element.querySelector('.session-chart').addEventListener('chartjs:connect', this.#onConnect.bind(this));
    }

    disconnect() {
        this.element.querySelector('.session-chart').removeEventListener('chartjs:connect', this.#onConnect.bind(this));
    }

    #onConnect(event) {
        const _self = this;
        setInterval(async function() {
            const response = await fetch('/sessions');
            const data = await response.json();
            _self.totalTarget.textContent = data.total;
            _self.usedTarget.textContent = data.used;
            _self.queuedTarget.textContent = data.queued;
            _self.pendingTarget.textContent = data.pending;
            data.sessions.forEach(function (session) {
                _self.element.querySelector('#' + CSS.escape(session.id)).textContent = session.count;
            });
            _self.#sessions.push({
                label: new Date().toLocaleTimeString('en-US'),
                data: {
                    total: data.total,
                    used: data.used,
                    queued: data.queued,
                    pending: data.pending,
                }
            });
            if (_self.#sessions.length > LIMIT) {
                _self.#sessions.shift();
            }
            event.detail.chart.data.labels = _self.#sessions.map( el => el.label );
            event.detail.chart.data.datasets[0].data = _self.#sessions.map( el => el.data.total );
            event.detail.chart.data.datasets[1].data = _self.#sessions.map( el => el.data.used );
            event.detail.chart.data.datasets[2].data = _self.#sessions.map( el => el.data.queued );
            event.detail.chart.data.datasets[3].data = _self.#sessions.map( el => el.data.pending );
            event.detail.chart.update();
        }, 5000);
    }
}
