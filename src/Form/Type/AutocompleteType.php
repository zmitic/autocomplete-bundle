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
use wjb\AutocompleteBundle\Service\ObjectTransformer;

class AutocompleteType extends AbstractType
{
    /** @var ObjectTransformer */
    private $objectTransformer;

    /** @var EntityManagerInterface|null */
    private $entityManager;

    public function __construct(ObjectTransformer $objectTransformer, EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
        $this->objectTransformer = $objectTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, [
            'attr' => [
                'data-wjb-autocomplete-id' => '',
                'autocomplete' => 'off',
            ]
        ]);
        $builder->add('value', TextType::class, [
            'attr' => [
                'data-wjb-autocomplete-value' => '',
                'placeholder' => $options['placeholder'],
            ],
            'label' => false,
        ]);
        $builder->addModelTransformer(new AutocompleteTransformer($options, $this->objectTransformer));
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

        $display = $options['display'];
        $suggestions = call_user_func($options['suggestions']);
        $suggestionsValues = array_map(function ($entity) use ($display) {
            return [
                'id' => (string)$entity->getId(),
                'value' => $this->objectTransformer->convertToValue($entity, $display),
            ];
        }, $suggestions);
        $view->vars['suggestions'] = $suggestionsValues;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'search',
        ]);

        $resolver->setDefault('placeholder', '');

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
            'attr' => [
                'class' => 'wjb-autocomplete-simple',
            ],
            'class' => null,
            'display' => null,
            'debounce' => 100,
            'error_mapping' => [
                '.' => 'value',
            ],
            'invalid_message' => 'This value is not valid, please select one from available choices',
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
            },
        ]);

        $resolver->setAllowedTypes('placeholder', ['string', 'null']);
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
