<?php

namespace wjb\AutocompleteBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use wjb\AutocompleteBundle\Form\Transformer\AutocompleteTransformer;

class AutocompleteType extends AbstractType
{
    /** @var EntityManagerInterface|null */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, [
            'attr' => [
                'data-id' => '',
                'autocomplete' => 'off',
            ]
        ]);
        $builder->add('value', TextType::class, [
            'attr' => [
                'data-value' => '',
            ],
            'label' => false,
        ]);
        $builder->addModelTransformer(new AutocompleteTransformer($options));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $parent = $form->getParent();
        if (!$parent) {
            throw new \InvalidArgumentException(sprintf('AutocompleteType form must be used within other class. Please read the documentation.'));
        }
        $parentInstance = $parent->getConfig()->getType()->getInnerType();

        $view->vars['form_class'] = base64_encode(get_class($parentInstance));
        $view->vars['form_field'] = base64_encode($form->getName());
        $view->vars['debounce'] = $options['debounce'];

        $suggestions = call_user_func($options['suggestions']);
        $suggestionsValues = array_map(function ($entity) {
            return [
                'id' => (string)$entity->getId(),
                'value' => (string)$entity,
            ];
        }, $suggestions);
        $view->vars['suggestions'] = $suggestionsValues;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'search',
        ]);

        $resolver->setDefault('find_one_by_id', function (Options $options) {
            return function ($id) use ($options) {
                if (!$this->entityManager) {
                    throw new TransformationFailedException('If not using default entity manager, you must provide "find_one_by_id" callback.');
                }
                $repository = $this->entityManager->getRepository($options['class']);

                return $repository->find($id);
            };
        });

        $resolver->setDefaults([
            'class' => null,
            'display' => null,
            'debounce' => 100,
            'error_mapping' => [
                '.' => 'value',
            ],
            'invalid_message' => 'Transformation failed',
            // if not found in DB, try looking by other columns
            'find_one_by_value' => function () {
                return null;
            },
            // this callback will receive $search parameter
            'not_found' => function () {
                throw new TransformationFailedException('Object not found.');
            },
            'suggestions' => function () {
                return [];
            }
        ]);

        $resolver->setAllowedTypes('class', ['string', 'null']);
        $resolver->setAllowedTypes('display', ['string', 'null', 'callable']);
        $resolver->setAllowedTypes('search', 'callable');
        $resolver->setAllowedTypes('suggestions', 'callable');
        $resolver->setAllowedTypes('find_one_by_id', 'callable');
        $resolver->setAllowedTypes('find_one_by_value', 'callable');
        $resolver->setAllowedTypes('not_found', 'callable');
        $resolver->setAllowedTypes('debounce', 'int');
    }
}

