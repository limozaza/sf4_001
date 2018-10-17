<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 17/10/18
 * Time: 23:13
 */

namespace App\Controller;


use App\Entity\Person;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class PersonController
 * @package App\Controller
 * @Route(path="/api/persons")
 */
class PersonController
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
     *     name="list_persons"
     * )
     * @Rest\View(
     *
     * )
     */
    public function list(){
        $persons = $this->em->getRepository(Person::class)->findAll();
        return $persons;
    }

    /**
     * @param Person|null $person
     * @return Person|\FOS\RestBundle\View\View|null
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="get_person"
     * )
     * @Rest\View()
     */
    public function get(?Person $person){
        if(null  === $person){
            return $this->view(null,404);
        }
        return $person;
    }


    /**
     * @Rest\Post(
     *     name="add_person"
     * )
     * @Rest\View(statusCode=201)
     * @ParamConverter("person", converter="fos_rest.request_body")
     */
    public function add(Person $person, ConstraintViolationListInterface $validationErrors){

        if(count($validationErrors)>0){
            throw new ValidationException($validationErrors);
        }

        $this->em->persist($person);
        $this->em->flush();
        return $this->view(
            $person,
            Response::HTTP_CREATED,
            [
                'Location' => $this->router->generate(
                    'get_person',
                    [
                        'id' => $person->getId(),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ]
                )
            ]
        );
    }

    /**
     * @Rest\Delete(
     *     path="/{id}",
     *     name="delete_person"
     * )
     * @Rest\View()
     */
    public function delete(?Person $person){
        if(null  === $person){
            return $this->view(null,404);
        }
        $this->em->remove($person);
        $this->em->flush();
    }

}