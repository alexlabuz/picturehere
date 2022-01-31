<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{
    #[Route('/api/post/add', name: 'post_add')]
    public function add(Request $request, Security $security, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // Image
        $nameImage = uniqid().".jpeg";
        $fileData = file_get_contents($request->files->get("picture"));
        file_put_contents("./data/".$nameImage, $fileData);

        // Post
        $postData = json_decode($request->request->get("post"));

        $post = new Post();
        $post->setMessage($postData->message);
        $post->setDate(new \DateTime());
        $post->setProfil($security->getUser()->getProfil());
        $post->setLinkImage("/data/".$nameImage);

        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json(['post' => "Post envoyé"]);
    }

    #[Route('/api/post/delete/{id}', name: 'post_delete')]
    public function delete(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PostController.php',
        ]);
    }
}
