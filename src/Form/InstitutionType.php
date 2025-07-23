<?php

namespace App\Form;

use App\Enum\InstitutionType as InstitutionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InstitutionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('postalCode', TextType::class, ['required' => false])
            ->add('country', TextType::class)
            ->add('establishedYear', IntegerType::class, ['required' => false])
            ->add('website', TextType::class, ['required' => false])
            ->add('type', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($type) => $type->label(), InstitutionTypeEnum::cases()),
                    InstitutionTypeEnum::cases()
                ),
                'choice_label' => fn(InstitutionTypeEnum $type) => $type->label(),
                'choice_value' => fn (?InstitutionTypeEnum $type) => $type?->value,
                'required' => true,
            ]);
    }
}
