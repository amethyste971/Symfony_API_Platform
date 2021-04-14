<?php
namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;  
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController {
    
    // private const POSTS = [
    //     [
    //         'id' => 1,
    //         'slug' => 'hello-world',
    //         'title' => 'Hello world!'
    //     ],
    //     [
    //         'id' => 2,
    //         'slug' => 'another-post',
    //         'title' => 'This is another post!'
    //     ],
    //     [
    //         'id' => 3,
    //         'slug' => 'last-exemple',
    //         'title' => 'This is the last exemple!'
    //     ],

    // ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page":5}, requirements={"page"="\d+"})
     */
    public function list($page = 1, Request $request) {
        // $response = new JsonResponse(self::POSTS);
        // // var_dump($response);
        // return $response;
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        $limit = $request->get('limit', 10);
        // return new Response($limit);

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function (BlogPost $item) {
                return $this->generateUrl('blog_by_slug',['slug' => $item->getSlug()]);
            },$items)
            ]);
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost")
     */
    public function post($post) {
        // return $this->json(
        //     $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        // );

        // var_dump($post);
        // var_dump($post->getComments());
        //It's the same as doing find($id) on repository
        return $this->json($post);
        // return new Response('Affiche l\'id' .$id);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost", options={"mapping":{"slug": "slug"}})
     */
    public function postBySlug($post) {
        // return $this->json(
        //     $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug])
        // );
        return $this->json($post);
    }

    /**
     * @Route("/add", name ="blog_add", methods={"POST"})
     */
    public function add(Request $request) {
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(),BlogPost::class,'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        
        return  $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(BlogPost $blogPost){
        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}


