<?php

namespace App\Form;

use App\Entity\Student;
use App\Enum\StudentStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName')
            ->add('email')
            ->add('dateOfBirth')
            ->add('gender')
            ->add('enrolmentYear')
            ->add('status', ChoiceType::class, [
                'choices' => StudentStatus::cases(),
                'choice_label' => fn(StudentStatus $status) => $status->label(),
                'choice_value' => fn(?StudentStatus $status) => $status?->value,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
