<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 15/10/18
 * Time: 22:45
 */

namespace App\Controller;
use App\Entity\Movie;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class MovieController
 * @package App\Controller
 * @Route(path="/api/movies")
 */
class MovieController
{
    use ControllerTrait;

    protected $em;
    protected $router;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @Rest\Get(
     *     name="list_movies"
     * )
     * @Rest\View(
     *
     * )
     */
    public function list(){
        $movies = $this->em->getRepository(Movie::class)->findAll();
        return $movies;
    }

    /**
     * @param Movie|null $movie
     * @return Movie|\FOS\RestBundle\View\View|null
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="get_movie"
     * )
     * @Rest\View()
     */
    public function get(?Movie $movie){
       if(null  === $movie){
           return $this->view(null,404);
       }
       return $movie;
    }


    /**
     * @Rest\Post(
     *     name="add_movie"
     * )
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     */
    public function add(Movie $movie, ConstraintViolationListInterface $validationErrors){

        if(count($validationErrors)>0){
            throw new ValidationException($validationErrors);
        }

        $this->em->persist($movie);
        $this->em->flush();
        return $this->view(
            $movie,
            Response::HTTP_CREATED,
            [
                'Location' => $this->router->generate(
                    'get_movie',
                    [
                        'id' => $movie->getId(),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ]
                )
            ]
        );
    }

    /**
     * @Rest\Delete(
     *     path="/{id}",
     *     name="delete_movie"
     * )
     * @Rest\View()
     */
    public function delete(?Movie $movie){
        if(null  === $movie){
            return $this->view(null,404);
        }
        $this->em->remove($movie);
        $this->em->flush();
    }

}