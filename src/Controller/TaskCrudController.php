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

use App\Form\Task\BrowserType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;

class TaskCrudController extends AbstractCrudController
{
    public function __construct(private HttpClientInterface $client, private string $statusUri)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('title');
        yield AssociationField::new('modelRevision', 'Model')
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $qb = $queryBuilder->getEntityManager()->createQueryBuilder();

                return $qb
                    ->select('m.activeRevision')
                    ->from(Model::class, 'm')
                    ->orderBy('m.label', 'DESC');
            })
            ->setRequired(true)
        ;
        yield ChoiceField::new('browser', 'Browser')->setChoices($this->getBrowserChoices())->setFormType(BrowserType::class);
    }

    private function getBrowserChoices(): array
    {
        $response = $this->client->request(
            'GET',
            rtrim($this->statusUri, '/') . '/status'
        );
        $choices = [];

        foreach ($response->toArray()['browsers'] ?? [] as $name => $browser) {
            foreach ($browser as $version => $session) {
                $choices[$name][$version] = sprintf('%s:%s', $name, $version);
            }
        }

        return $choices;
    }
}
