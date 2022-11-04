<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClassroomController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response {
        $classrooms = $doctrine->getRepository(Classroom::class)->findAll();

        return $this->render('classroom/index.html.twig', array('classrooms' => $classrooms));
    }

    public function new(ManagerRegistry $doctrine, Request $request): Response {
        $classroom = new Classroom();

        $form = $this->createFormBuilder($classroom)
            ->add('subject', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('teacher', EntityType::class, array('class' => User::class, 'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')->where('JSON_CONTAINS(u.roles, :role) = 1')->setParameter('role', '"ROLE_TEACHER"');
            }, 'choice_label' => 'username'))
            ->add('currentCount', IntegerType::class, array('attr' => array('class' => 'form-control')))
            ->add('maxCount', IntegerType::class, array('attr' => array('class' => 'form-control')))
            ->add('meetingTimes', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $classroom = $form->getData();
                $em = $doctrine->getManager();
                $em->persist($classroom);
                $em->flush();

                return $this->redirectToRoute('classroom');
            }

        return $this->render('classroom/new.html.twig', array('form' => $form->createView()));
    }

    public function join(ManagerRegistry $doctrine, $id) {
        $loggedUser = $this->getUser();
        $permissions = $loggedUser->getRoles();
        $classroom = $doctrine->getRepository(Classroom::class)->find($id);

        if (in_array('ROLE_STUDENT', $permissions)) {
            $em = $doctrine->getManager();
            $user = $doctrine->getRepository(User::class)->find($loggedUser);     
            
            $currentCount = $classroom->getCurrentCount();
            $maxCount = $classroom->getMaxCount();
            $classes = $user->getClasses();
            
            if (!in_array($classroom, $classes) && $currentCount < $maxCount) {
                $currentCount++;
                $classroom->setCurrentCount($currentCount);
                $classroom->addUser($user);
                $user->addClass($classroom);
                $em->flush();

                return $this->redirectToRoute('classroom');
            }
        }

        return $this->render('classroom/join.html.twig', array('classroom' => $classroom));
    }

    public function leave(ManagerRegistry $doctrine, $id) {
        $classroom = $doctrine->getRepository(Classroom::class)->find($id);   
        $loggedUser = $this->getUser();
        $permissions = $loggedUser->getRoles();

        if (in_array('ROLE_STUDENT', $permissions)) {
            $em = $doctrine->getManager();
            $user = $doctrine->getRepository(User::class)->find($loggedUser);
            $currentCount = $classroom->getCurrentCount();
            $classes = $user->getClasses();

            if (in_array($classroom, $classes)) {
                $currentCount--;
                $classroom->setCurrentCount($currentCount);
                $classroom->removeUser($user);
                $user->removeClass($classroom);
                $em->flush();
    
                return $this->redirectToRoute('classroom');
            }
        }

        return $this->render('classroom/leave.html.twig', array('classroom' => $classroom));
    }


    public function show(ManagerRegistry $doctrine, Request $request, $id) {
        $classroom = $doctrine->getRepository(Classroom::class)->find($id);

        $form = $this->createFormBuilder($classroom)
            ->add('syllabus', FileType::class, array('data_class' => null, 'label' => 'Upload a syllabus'))
            ->add('save', SubmitType::class, array('label' => 'Upload', 'attr' => array('class' => 'btn btn-primary btn-sm')))
            ->getForm();

            $form->handleRequest($request);
            $imagePath = $classroom->getSyllabus();
            $image = substr(strrchr($imagePath, "\\"), 1);

            if ($form->isSubmitted() && $form->isValid()){
                $classroom = $form->getData();
                $uploads_directory = $this->getParameter('uploads_directory');

                $filename = $form['syllabus']->getData();
                $form['syllabus']->getData()->move($uploads_directory, $filename);

                $em = $doctrine->getManager();
                $em->persist($classroom);
                $em->flush();

                return $this->redirectToRoute('classroom');
            }

        return $this->render('classroom/show.html.twig', array('classroom' => $classroom, 'form' => $form->createView(), 'image' => $image));
    }
}
