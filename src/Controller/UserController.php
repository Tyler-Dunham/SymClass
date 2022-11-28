<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\User;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    public function enrollment(ManagerRegistry $doctrine): Response {
        $loggedUser = $this->getUser();
        $user = $doctrine->getRepository(User::class)->find($loggedUser);
        $permissions = $loggedUser->getRoles();
        
        if (in_array('ROLE_TEACHER', $permissions)){
            $classrooms = $doctrine->getRepository(Classroom::class)->findBy(array('teacher' => $user->getId()));
        }
        elseif (in_array('ROLE_STUDENT', $permissions)) {
            $classrooms = $user->getClasses();
        }

        return $this->render('user/index.html.twig', array('classrooms' => $classrooms, 'user' => $user));
    }
}
