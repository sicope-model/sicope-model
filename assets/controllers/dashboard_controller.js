import { Controller } from '@hotwired/stimulus';

const LIMIT = 6;

export default class extends Controller {
    static targets = [ 'total', 'used', 'queued', 'pending' ]
    #data = []
    #sessionChart = null
    #messageChart = null
    #interval

    connect() {
        this.element.querySelector('.session-chart').addEventListener('chartjs:connect', this.#onSessionChartConnect.bind(this));
        this.element.querySelector('.message-chart').addEventListener('chartjs:connect', this.#onMessageChartConnect.bind(this));
        const _self = this;
        this.#interval = setInterval(async function() {
            const response = await fetch('/status');
            const data = await response.json();
            _self.totalTarget.textContent = data.sessions.total;
            _self.usedTarget.textContent = data.sessions.used;
            _self.queuedTarget.textContent = data.sessions.queued;
            _self.pendingTarget.textContent = data.sessions.pending;
            data.sessions.sessions.forEach(function (session) {
                _self.element.querySelector('#' + CSS.escape(session.id)).textContent = session.count;
            });
            _self.#data.push({
                label: new Date().toLocaleTimeString('en-US'),
                sessions: {
                    total: data.sessions.total,
                    used: data.sessions.used,
                    queued: data.sessions.queued,
                    pending: data.sessions.pending,
                },
                messages: {
                    all: data.messages.all,
                    errors: data.messages.errors
                }
            });
            if (_self.#data.length > LIMIT) {
                _self.#data.shift();
            }
            if (_self.#sessionChart) {
                _self.#sessionChart.data.labels = _self.#data.map(el => el.label);
                _self.#sessionChart.data.datasets[0].data = _self.#data.map(el => el.sessions.total);
                _self.#sessionChart.data.datasets[1].data = _self.#data.map(el => el.sessions.used);
                _self.#sessionChart.data.datasets[2].data = _self.#data.map(el => el.sessions.queued);
                _self.#sessionChart.data.datasets[3].data = _self.#data.map(el => el.sessions.pending);
                _self.#sessionChart.update();
            }
            if (_self.#messageChart) {
                _self.#messageChart.data.labels = _self.#data.map(el => el.label);
                _self.#messageChart.data.datasets[0].data = _self.#data.map(el => el.messages.all);
                _self.#messageChart.data.datasets[1].data = _self.#data.map(el => el.messages.errors);
                _self.#messageChart.update();
            }
        }, 5000);
    }

    disconnect() {
        this.element.querySelector('.session-chart').removeEventListener('chartjs:connect', this.#onSessionChartConnect.bind(this));
        this.element.querySelector('.message-chart').removeEventListener('chartjs:connect', this.#onMessageChartConnect.bind(this));
        clearInterval(this.#interval);
    }

    #onSessionChartConnect(event) {
        this.#sessionChart = event.detail.chart;
    }

    #onMessageChartConnect(event) {
        this.#messageChart = event.detail.chart;
    }
}
