<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
        ]);

        return $this->json(["Utilisateur" => json_decode($profil)]);
    }
}
