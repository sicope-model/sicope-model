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

use App\Form\Model\RevisionType;
use App\Form\ModelImportType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Model\Model\RevisionInterface;
use Tienvx\Bundle\MbtBundle\Model\ModelInterface;

class ModelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Model::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('label');
        yield TextField::new('tags')
            ->setFormTypeOptions([
                'attr' => [
                    'data-controller' => 'tags',
                ],
            ])
            ->addWebpackEncoreEntries('app');
        yield HiddenField::new('activeRevision', 'Revision')
            ->formatValue(function (RevisionInterface $value) {
                return sprintf('id %d, %d place(s), %d transition(s)', $value->getId(), \count($value->getPlaces()), \count($value->getTransitions()));
            })
            ->setFormType(RevisionType::class)
            ->setFormTypeOptions([
                'label' => false,
                'attr' => [
                    'data-controller' => 'places',
                    'data-action' => 'place-label:added->places#addOption place-label:removed@window->places#removeOption place-label:updated->places#updateOption places-select:added->places#setOptions',
                ],
            ])
            ->addCssClass('field-collection')
            ->addJsFiles('bundles/easyadmin/form-type-collection.js')
            ->setDefaultColumns('col-md-8 col-xxl-7');
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportModel = Action::new('exportModel', 'Export Model', 'fa fa-file-export')
            ->linkToCrudAction('exportModel');
        $importModel = Action::new('importModel', 'Import Model', 'fa fa-file-import')
            ->linkToRoute('app_import_model')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $exportModel)
            ->add(Crud::PAGE_INDEX, $importModel);
    }

    public function exportModel(AdminContext $context): JsonResponse
    {
        $model = $context->getEntity()->getInstance();

        return $this->json($model->toArray(), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getLabel() . '.json'
            ),
        ])->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_PRETTY_PRINT);
    }

    #[Route('/models/import', name: 'app_import_model')]
    public function importModel(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelImportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ModelInterface $model */
            $model = $form->get('model')->getData();
            $em->persist($model);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'Imported model');

            return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl());
        }

        return $this->render('importModel.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
