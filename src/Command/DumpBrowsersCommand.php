<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Command;

use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\WebDriverPlatform;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tienvx\Bundle\MbtBundle\Provider\ProviderManagerInterface;
use Tienvx\Bundle\MbtBundle\Provider\Selenoid;

class DumpBrowsersCommand extends Command
{
    protected static $defaultName = 'app:dump-browsers';
    protected ProviderManagerInterface $providerManager;

    public function __construct(ProviderManagerInterface $providerManager, string $name = null)
    {
        $this->providerManager = $providerManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription("Dump selenoid's configuration into browsers.json")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $provider = Selenoid::getName();
        $browsers = [];

        foreach ($this->providerManager->getPlatforms($provider) as $platform) {
            foreach ($this->providerManager->getBrowsers($provider, $platform) as $browser) {
                // https://github.com/aerokube/charts/blob/master/moon/values.yaml
                switch ($browser) {
                    case WebDriverBrowserType::MICROSOFT_EDGE:
                        $key = 'edge';
                        $prefix = 'browsers/edge:';
                        $path = '/';
                        break;
                    case WebDriverBrowserType::SAFARI:
                        $key = 'safari';
                        $prefix = 'browsers/safari:';
                        $path = '/';
                        break;
                    case WebDriverBrowserType::FIREFOX:
                        $key = 'firefox';
                        $prefix = 'selenoid/vnc:firefox_';
                        $path = '/wd/hub';
                        break;
                    case WebDriverBrowserType::CHROME:
                        $key = WebDriverPlatform::ANDROID === $platform ? 'chrome-mobile' : 'chrome';
                        $prefix = WebDriverPlatform::ANDROID === $platform ?
                            'selenoid/chrome-mobile:' :
                            'selenoid/vnc:chrome_';
                        $path = WebDriverPlatform::ANDROID === $platform ? '/wd/hub' : '/';
                        break;
                    case WebDriverBrowserType::ANDROID:
                        $key = 'android';
                        $prefix = 'selenoid/android:';
                        $path = '/wd/hub';
                        break;
                    case WebDriverBrowserType::OPERA:
                        $key = 'opera';
                        $prefix = 'selenoid/vnc:opera_';
                        $path = '/';
                        break;
                    default:
                        $key = $browser;
                        $prefix = "selenoid/vnc:{$browser}_";
                        $path = '/wd/hub';
                }
                foreach ($this->providerManager->getBrowserVersions($provider, $platform, $browser) as $version) {
                    if (!isset($default) || version_compare($version, $default) >= 0) {
                        $default = $version;
                        $browsers[$key]['default'] = $default;
                    }
                    $browsers[$key]['versions'][$version] = [
                        'image' => $prefix . $version,
                        'port' => '4444',
                        'path' => $path,
                    ];
                }
            }
        }

        if ($browsers) {
            $path = \dirname(__DIR__) . '/../var/selenoid/browsers.json';
            $io->note(sprintf('Dumping %d browsers into %s', \count($browsers), realpath($path)));
            file_put_contents($path, json_encode($browsers, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) . "\n");

            $io->success(sprintf('Dumped %d browsers into %s.', \count($browsers), realpath($path)));
        }

        return Command::SUCCESS;
    }
}
