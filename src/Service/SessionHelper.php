<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SessionHelper
{
    public function __construct(
        private HttpClientInterface $client,
        private string $statusUri,
        private BrowserFormatter $formatter
    ) {
    }

    public function getBrowserChoices(): array
    {
        $choices = [];

        foreach ($this->getStatus()['browsers'] ?? [] as $name => $versions) {
            foreach ($versions as $version => $session) {
                $choices[$this->formatter->format($name, $version)] = sprintf('%s:%s', $name, $version);
            }
        }

        return $choices;
    }

    public function getSessionStatistics(): array
    {
        $status = $this->getStatus();
        $browsers = $status['browsers'] ?? [];
        $sessions = [];
        foreach ($browsers as $browser => $versions) {
            foreach ($versions as $version => $users) {
                $sessions[] = [
                    'id' => "$browser:$version",
                    'browser' => $this->formatter->format($browser, $version),
                    'icon' => 'chrome-mobile' === $browser ? 'fa fa-mobile-alt' : "fab fa-$browser",
                    'count' => array_reduce($users, fn (int $total, array $user) => $total + $user['count'], 0),
                ];
            }
        }

        return [
            'total' => $status['total'] ?? 0,
            'used' => $status['used'] ?? 0,
            'queued' => $status['queued'] ?? 0,
            'pending' => $status['pending'] ?? 0,
            'sessions' => $sessions,
        ];
    }

    protected function getStatus(): array
    {
        try {
            return $this->client->request(
                'GET',
                rtrim($this->statusUri, '/') . '/status'
            )->toArray();
        } catch (TransportExceptionInterface $e) {
            return [];
        }
    }
}
