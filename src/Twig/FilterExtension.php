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

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig Custom Filters.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class FilterExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('timeDiff', [$this, 'timeDiff'], ['needs_environment' => true]),
            new TwigFilter('phoneFormat', [$this, 'phoneFormat']),
            new TwigFilter('basename', [$this, 'basename']),
        ];
    }

    /**
     * Time Ago.
     */
    public function timeDiff(
        Environment $env,
        $date,
        $now = null,
        $text = 'diff.ago',
        $domain = 'messages',
        $length = 1
    ): string {
        $units = [
            'y' => $this->translator->trans('diff.year', [], $domain),
            'm' => $this->translator->trans('diff.month', [], $domain),
            'd' => $this->translator->trans('diff.day', [], $domain),
            'h' => $this->translator->trans('diff.hour', [], $domain),
            'i' => $this->translator->trans('diff.minute', [], $domain),
            's' => $this->translator->trans('diff.second', [], $domain),
        ];

        // Date Time
        $date = twig_date_converter($env, $date); /* @phpstan-ignore-line */
        $now = twig_date_converter($env, $now); /* @phpstan-ignore-line */

        // Convert
        $diff = $date->diff($now);
        $format = '';

        $counter = 0;
        foreach ($units as $key => $val) {
            $count = $diff->$key;

            if (0 !== $count) {
                $format .= $count . ' ' . $val . ' ';

                ++$counter;
                if ($counter === $length) {
                    break;
                }
            }
        }

        return $format ? $format . $this->translator->trans($text, [], $domain) : '';
    }

    /**
     * Phone Formatter.
     */
    public function phoneFormat($phone): string
    {
        // Null | Empty | 0
        if (empty($phone) || 0 === $phone) {
            return '';
        }

        return mb_substr($phone, 0, 3) . '-' . mb_substr($phone, 3, 3) . '-' . mb_substr($phone, 6);
    }

    /**
     * Basename Formatter.
     */
    public function basename($path): string
    {
        return basename($path);
    }
}
