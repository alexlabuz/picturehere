<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProfilController extends AbstractController
{
    #[Route('/api/profil/connected', name: 'connected')]
    public function connecter(Security $security, SerializerInterface $serializer): Response
    {
        $profil = $serializer->serialize($security->getUser(), 'json', [
            "groups" => "profil",
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.v\Z',
        ]);

        return $this->json(["Utilisateur" => json_decode($profil)]);
    }
    
    #[Route('/api/profil/update', name: 'update_profil')]
    public function update(Request $request, Security $security, SerializerInterface $serializer, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordhash): Response
    {
        if($request->isMethod("POST")){
            $entityManager = $doctrine->getManager();
            $data = $request->toArray();

            $user = $entityManager->getRepository(User::class)->find($security->getUser()->getId());

            if(isset($data["pseudo"]) && strlen($data["pseudo"]) > 0){
                $user->getProfil()->setPseudo($data["pseudo"]);
            }

            if(isset($data["password"]) && strlen($data["password"]) > 0){
                $user->setPassword($passwordhash->hashPassword($user, $data["password"]));
            }
            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json(['message' => "Profil modifié"]);
        }

        return $this->json(["message" => "require post data"]);
    }
}
