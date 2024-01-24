<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
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
                'label' => 'Titre du ticket'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu du ticket'
            ])
            ->add('project', EntityType::class,[
                'class' => Project::class,
                'label' => 'Sélectionner un projet',
                'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                    return $entityRepository->createQueryBuilder('p')
                        ->where('p.company = :company')
                        ->setParameter('company', $this->security->getUser()->getCompany());
                }
            ])
            ->add('type', EntityType::class, [
                'class' => \App\Entity\TicketType::class,
                'label' => 'Sélectionner le type du ticket'
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'label' => 'Sélectionner le statut du ticket'
            ])
            ->add('Nouveau', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            ''
        ]);
    }
}
