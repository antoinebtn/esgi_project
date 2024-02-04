<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Ticket;
use App\Entity\TicketType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTicketType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre du ticket'
            ])
            ->add('content', TinymceType::class, [
                'required' => false,
                'label' => 'Contenu du ticket',
                "attr" => [
                    "toolbar" => "undo redo | bold italic underline forecolor fontsize fontfamily | alignleft aligncenter alignright | outdent indent | h1 h2 h3 | styles"
                ]
            ])
            ->add('endDate', DateTimeType::class,[
                'required' => false,
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('assignment', EntityType::class, [
                'required' => false,
                'class' => User::class,
                'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                    return $entityRepository->createQueryBuilder('u')
                        ->where('u.company = :company')
                        ->setParameter('company', $this->security->getUser()->getCompany());
                }
            ])
            ->add('project', EntityType::class,[
                'required' => false,
                'class' => Project::class,
                'label' => 'Sélectionner un projet',
                'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                    return $entityRepository->createQueryBuilder('p')
                        ->where('p.company = :company')
                        ->setParameter('company', $this->security->getUser()->getCompany());
                }
            ])
            ->add('type', EntityType::class, [
                'required' => false,
                'class' => \App\Entity\TicketType::class,
                'label' => 'Sélectionner le type du ticket'
            ])
            ->add('Sauvegarder', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
