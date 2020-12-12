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

namespace App\Form\Testing;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\SeleniumConfig;
use Tienvx\Bundle\MbtBundle\Provider\ProviderManager;

class SeleniumConfigType extends AbstractType
{
    protected ProviderManager $providerManager;

    public function __construct(ProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (FormInterface $form, string $provider, string $platform, string $browser) {
            $form->add('provider', ChoiceType::class, [
                'label' => 'task_provider',
                'choices' => $this->providerManager->all(),
                'choice_label' => fn ($provider) => $provider,
                'attr' => [
                    'class' => 'providers',
                ],
                'data' => $provider,
            ]);

            $form->add('platform', ChoiceType::class, [
                'label' => 'task_platform',
                'choices' => $this->providerManager->get($provider)->getPlatforms(),
                'attr' => [
                    'class' => 'platforms',
                ],
                'choice_label' => fn ($platform) => $platform,
                'data' => $platform,
            ]);

            $form->add('browser', ChoiceType::class, [
                'label' => 'task_browser',
                'choices' => $this->providerManager->get($provider)->getBrowsers($platform),
                'attr' => [
                    'class' => 'browsers',
                ],
                'choice_label' => fn ($browser) => $browser,
                'data' => $browser,
            ]);

            $form->add('browserVersion', ChoiceType::class, [
                'label' => 'task_browser_version',
                'choices' => $this->providerManager->get($provider)->getBrowserVersions($platform, $browser),
                'attr' => [
                    'class' => 'browser-versions',
                ],
                'choice_label' => fn ($version) => $version,
            ]);

            $form->add('resolution', ChoiceType::class, [
                'label' => 'task_resolution',
                'choices' => $this->providerManager->get($provider)->getResolutions($platform),
                'attr' => [
                    'class' => 'resolutions',
                ],
                'choice_label' => fn ($resolution) => $resolution,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $providers = $this->providerManager->all();
                $provider = reset($providers);
                $platforms = $this->providerManager->get($provider)->getPlatforms();
                $platform = reset($platforms);
                $browsers = $this->providerManager->get($provider)->getBrowsers($platform);
                $browser = reset($browsers);

                $formModifier($event->getForm(), $provider, $platform, $browser);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier($event->getForm(), $data['provider'], $data['platform'], $data['browser']);
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
