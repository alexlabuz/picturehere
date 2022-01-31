<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/user/register', name: 'register')]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordhash): Response
    {
        if($request->isMethod("post")){
            $entityManager = $doctrine->getManager();
            $data = $request->toArray();

            $user = new User();
            $user->setUsername($data["username"]);
            $user->setPassword($passwordhash->hashPassword($user, $data["password"]));

            $profil = new Profil();
            $profil->setPseudo($data["pseudo"]);
            $profil->setDateInscription(new \DateTime());

            $user->setProfil($profil);

            $entityManager->persist($user);
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->json(['message' => "Utilisateur inscrit"]);
        }
        return $this->json(['message' => "Require post data"]);
    }
}
