<?php

namespace App\Form;

use App\Entity\Snowtrick;
use App\Enum\SnowtrickCategories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnowtrickType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Title',
				'required' => true,
				'attr' => [
					'class' => 'form-control form-control-lg',
					'placeholder' => 'Enter trick title',
				],
			])
			->add('description', TextareaType::class, [
				'label' => 'Description',
				'required' => true,
				'attr' => [
					'class' => 'form-control',
					'placeholder' => 'Describe the trick',
					'rows' => 6,
				],
			])
			->add('category', EnumType::class, [
				'class' => SnowtrickCategories::class,
				'label' => 'Category',
				'required' => true,
				'attr' => [
					'class' => 'form-select',
				],
				'choice_label' => function (SnowtrickCategories $category) {
					return $category->value;
				},
			])
			->add('media', CollectionType::class, [
				'label' => false,
				'entry_type' => FileType::class,
				'entry_options' => [
					'snowtrick' => $options['data'] ?? null,
				],
				'allow_add' => true,
				'allow_delete' => true,
				'by_reference' => false,
				'mapped' => false,
				'required' => false,
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Snowtrick::class,
			'attr' => [
				'novalidate' => 'novalidate',
			]
		]);
	}
}
