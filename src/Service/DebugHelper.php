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

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tienvx\Bundle\MbtBundle\Service\SelenoidHelperInterface;

class DebugHelper
{
    public function __construct(private SelenoidHelperInterface $selenoidHelper)
    {
    }

    public function streamVideo(string $session): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($session) {
            echo file_get_contents(
                $this->selenoidHelper->getVideoUrl($session)
            );
        });

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            "$session.mp4"
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->send();

        return $response;
    }

    public function getLog(string $session): Response
    {
        return new Response(file_get_contents($this->selenoidHelper->getLogUrl($session)));
    }
}
