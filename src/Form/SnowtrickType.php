<?php

namespace App\Form;

use App\Entity\Snowtrick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
				'label' => 'Nom',
				'attr' => [
					'class' => 'form-control',
					'placeholder' => 'Nom de la figure',
				],
			])
			->add('description', TextareaType::class, [
				'label' => 'Description',
				'attr' => [
					'class' => 'form-control',
					'placeholder' => 'Description de la figure',
				],
			])
			->add('video', TextType::class, [
				'label' => 'Vidéo',
				'attr' => [
					'class' => 'form-control',
					'placeholder' => 'Lien de la vidéo',
				],
			])
			->add('images', CollectionType::class, [
				'label' => 'Images',
				'attr' => [
					'class' => 'form-control',
				],
				'entry_type' => FileType::class,
				'multiple' => true,
				'by_reference' => false,
				'mapped' => false,
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Snowtrick::class,
			'attr' => [
				'novalidate' => 'novalidate',
			],
		]);
	}

}
