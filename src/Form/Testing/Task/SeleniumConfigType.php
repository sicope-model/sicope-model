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

namespace App\Form\Testing\Task;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Task\SeleniumConfig;
use Tienvx\Bundle\MbtBundle\Provider\ProviderManagerInterface;

class SeleniumConfigType extends AbstractType
{
    protected ProviderManagerInterface $providerManager;

    public function __construct(ProviderManagerInterface $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (
            FormInterface $form,
            ?string $provider = null,
            ?string $platform = null,
            ?string $browser = null
        ) {
            $providers = $this->providerManager->getProviders();
            $provider = \in_array($provider, $providers) ? $provider : reset($providers);
            $platforms = $this->providerManager->getPlatforms($provider);
            $platform = \in_array($platform, $platforms) ? $platform : reset($platforms);
            $browsers = $this->providerManager->getBrowsers($provider, $platform);
            $browser = \in_array($browser, $browsers) ? $browser : reset($browsers);

            $form->add('provider', ChoiceType::class, [
                'label' => 'task_provider',
                'choices' => $this->providerManager->getProviders(),
                'choice_label' => fn ($provider) => $provider,
                'attr' => [
                    'class' => 'providers',
                ],
                'data' => $provider,
            ]);

            $form->add('platform', ChoiceType::class, [
                'label' => 'task_platform',
                'choices' => $this->providerManager->getPlatforms($provider),
                'attr' => [
                    'class' => 'platforms',
                ],
                'choice_label' => fn ($platform) => $platform,
                'data' => $platform,
            ]);

            $form->add('browser', ChoiceType::class, [
                'label' => 'task_browser',
                'choices' => $this->providerManager->getBrowsers($provider, $platform),
                'attr' => [
                    'class' => 'browsers',
                ],
                'choice_label' => fn ($browser) => $browser,
                'data' => $browser,
            ]);

            $form->add('browserVersion', ChoiceType::class, [
                'label' => 'task_browser_version',
                'choices' => $this->providerManager->getBrowserVersions($provider, $platform, $browser),
                'attr' => [
                    'class' => 'browser-versions',
                ],
                'choice_label' => fn ($version) => $version,
            ]);

            $form->add('resolution', ChoiceType::class, [
                'label' => 'task_resolution',
                'choices' => $this->providerManager->getResolutions($provider, $platform),
                'attr' => [
                    'class' => 'resolutions',
                ],
                'choice_label' => fn ($resolution) => $resolution,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier(
                    $event->getForm(),
                    $data['provider'] ?? null,
                    $data['platform'] ?? null,
                    $data['browser'] ?? null
                );
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SeleniumConfig::class,
        ]);
    }
}
