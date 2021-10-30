<?php

namespace App\Service;

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

    protected function getStatus(): array
    {
        return $this->client->request(
            'GET',
            rtrim($this->statusUri, '/') . '/status'
        )->toArray();
    }
}
