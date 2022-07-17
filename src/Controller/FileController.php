<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route('/files', name: 'app_files')]
    public function manageFiles(): Response
    {
        return $this->render('files_manager.html.twig');
    }

    #[Route('/file-suggestions', name: 'app_file_suggestions')]
    public function files(): JsonResponse
    {
        $finder = new Finder();
        $finder->files()->in($this->getParameter('app.upload_dir'));

        $files = [];
        foreach ($finder as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $file->getRelativePathname(),
            ];
        }

        return $this->json(['files' => $files]);
    }
}
