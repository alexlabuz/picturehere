<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PostController extends AbstractController
{
    #[Route('/api/post/thread', name: 'post_thread')]
    public function thread(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $entityManager = $doctrine->getManager();

        $postsRepo = $entityManager->getRepository(Post::class)->findAll();

        $posts = $serializer->serialize($postsRepo, 'json', [
            "groups" => "thread",
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
        ]);

        return $this->json(json_decode($posts));
    }

    #[Route('/api/post/add', name: 'post_add')]
    public function add(Request $request, Security $security, ManagerRegistry $doctrine, ImageService $imageService): Response
    {
        if($request->isMethod("post")){
            $entityManager = $doctrine->getManager();

            // Image
            $nameImage = uniqid().".jpeg";
            $fileData = file_get_contents($request->files->get("picture"));
            $imageService->savePostImage($fileData, "./data/".$nameImage);

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
        return $this->json(['message' => "Require post data"], 500);
    }

    #[Route('/api/post/delete/{id}', name: 'post_delete')]
    public function delete(Request $request, Security $security, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if($post == null) return $this->json(["Erreur" => "Le post n'existe pas"], 404);
        if($post->getProfil()->GetId() != $security->getUser()->getProfil()->getId()) return $this->json(["Erreur" => "Ce post ne vous appertient pas"], 403);

        $entityManager->remove($post);
        unlink(".".$post->getLinkImage());
        $entityManager->flush();

        return $this->json(['message' => 'Post supprimé']);
    }
}
