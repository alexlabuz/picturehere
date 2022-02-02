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
use Symfony\Component\Security\Core\Security;

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
        return $this->json(['message' => "Require post data"], 500);
    }

    #[Route('/api/user/delete', name: 'delete_account')]
    public function deleteAccount(Request $request, Security $security, ManagerRegistry $doctrine): Response
    {
        if($request->isMethod("post") && $request->request->get("id") !== null){
            $entityManager = $doctrine->getManager();
            $user = $entityManager->getRepository(User::class)->find($request->request->get("id"));

            if($user == null) return $this->json(["error" => "Cet utilisateur n'existe pas"], 404);
            if($user->getId() != $security->getUser()->getId()) return $this->json(["error" => "Cet utilisateur n'est pas vous"], 403);

            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json(['message' => "Utilisateur supprimé"]);
        }
        return $this->json(['message' => "Require post data and id parameters"], 500);
    }
}
