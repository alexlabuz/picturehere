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
    public function thread(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $entityManager = $doctrine->getManager();

        $postsRepo = $entityManager->getRepository(Post::class)->findBy(
            [],
            ["date" => "DESC"],
            20
        );

        $posts = $serializer->serialize($postsRepo, 'json', [
            "groups" => "thread",
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.v\Z',
        ]);

        return $this->json(json_decode($posts));
    }

    #[Route('/api/post/user/{id}', name: 'post_user')]
    public function postByUser(ManagerRegistry $doctrine, SerializerInterface $serializer, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $postsRepo = $entityManager->getRepository(Post::class)->findBy(
            ["profil" => $id],
            ["date" => "DESC"],
            20
        );

        $posts = $serializer->serialize($postsRepo, 'json', [
            "groups" => "thread",
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s.v\Z',
        ]);

        return $this->json(json_decode($posts));
    }

    #[Route('/api/post/add', name: 'post_add')]
    public function add(Request $request, Security $security, ManagerRegistry $doctrine, ImageService $imageService): Response
    {
        if($request->isMethod("post")){
            if($request->files->get("picture") == null) return $this->json(['message' => "Picture required"], 401);
            if($request->request->get("post") == null) return $this->json(['message' => "Post required"], 401);

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

            return $this->json(['message' => "Post envoy??"]);
        }
        return $this->json(['message' => "Require post data"], 401);
    }

    #[Route('/api/post/delete/{id}', name: 'post_delete')]
    public function delete(Request $request, Security $security, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if($post == null) return $this->json(["message" => "Le post n'existe pas"], 404);
        if($post->getProfil()->GetId() != $security->getUser()->getProfil()->getId()) return $this->json(["message" => "Ce post ne vous appertient pas"], 403);

        $entityManager->remove($post);
        unlink(".".$post->getLinkImage());
        $entityManager->flush();

        return $this->json(['message' => 'Post supprim??']);
    }
}
