<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Website;
use App\Entity\Wishlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('email')
            ->add('isLocked')
            ->add('contributingWishlists', EntityType::class, [
                'class' => Wishlist::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('invitedWishlists', EntityType::class, [
                'class' => Wishlist::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('website', EntityType::class, [
                'class' => Website::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
