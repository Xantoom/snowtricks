<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('type', ChoiceType::class, [
				'choices' => [
					'Image' => 'image',
					'Video' => 'video'
				],
				'attr' => [
					'class' => 'media-type-selector form-select'
				]
			])
			->add('id', HiddenType::class, [
				'mapped' => false,
				'required' => false
			]);

		// Add fields conditionally based on selected type
		$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
			$form = $event->getForm();
			$file = $event->getData();

			if ($file && $file->getType() === 'video') {
				$form->add('path', UrlType::class, [
					'label' => 'Video URL',
					'attr' => [
						'placeholder' => 'https://www.youtube.com/embed/...',
						'class' => 'form-control video-url'
					]
				]);
			} else {
				$form->add('imageFile', SymfonyFileType::class, [
					'label' => 'Upload Image',
					'mapped' => false,
					'required' => false,
					'attr' => [
						'class' => 'form-control image-upload'
					]
				]);
			}
		});
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => File::class,
			'snowtrick' => null,
			'attr' => [
				'novalidate' => 'novalidate',
			]
		]);
	}
}
