<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Ticket;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('title')
            ->add('content')
            ->add('project', EntityType::class,[
                'class' => Project::class,
                'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                    return $entityRepository->createQueryBuilder('p')
                        ->where('p.company = :company')
                        ->setParameter('company', $this->security->getUser()->getCompany());
                }
            ])
            ->add('type')
            ->add('status')
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
