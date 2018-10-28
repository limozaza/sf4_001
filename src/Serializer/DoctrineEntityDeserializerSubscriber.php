<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 25/10/18
 * Time: 22:10
 */

namespace App\Serializer;


use App\Annotation\DeserialEntity;
use App\Annotation\DeserializeEntity;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(Reader $annotationReader, EntityManagerInterface $entityManager)
    {
        $this->annotationReader = $annotationReader;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event){
        //Recuperetion du type de la classe a deserializer
        $deserializeType = $event->getType()['name'];
        //Verifier si le type de classe n'existe pas
        if(!class_exists($deserializeType)){
            return;
        }
        //Recuperer les data a deserializer
        $data = $event->getData();
        //Recuperer les informations de la classe a partir du type
        $class = new \ReflectionClass($deserializeType);

        foreach ($class->getProperties() as $property){
            if(!isset($data[$property->name])){
                continue;
            }
            /**
             * @var DeserializeEntity $annotation
             * Recuperer les 4 variable de l'annotation DeserializeEntity
             */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if(null === $annotation || !class_exists($annotation->type)){
                continue;
            }
            $data[$property->name] = [
                $annotation->idField => $data[$property->name]
            ];
        }
        $event->setData($data);
    }
    public function onPostDeserialize(ObjectEvent $event){
        $deserializedType = $event->getType()['name'];
        if(!class_exists($deserializedType)){
            return;
        }
        $object = $event->getObject();
        $reflection = new \ReflectionObject($object);
        foreach ($reflection->getProperties() as $property){
            /**
             * @var DeserializeEntity $annotation
             */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if(null === $annotation || !class_exists($annotation->type)){
                continue;
            }
            if(!$reflection->hasMethod($annotation->setter)){
                throw new \LogicException(
                    "Object {$reflection->getName()} does not have the {$annotation->setter} method"
                );
            }
            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);
            if(null === $deserializedEntity){
                return;
            }
            $entityId = $deserializedEntity->{$annotation->idGetter}();
            $repository = $this->entityManager->getRepository($annotation->type);
            $entity = $repository->find($entityId);
            if(null=== $entity){
                throw new NotFoundHttpException(
                    "Ressource {$reflection->getShortName()}/$entityId"
                );
            }
            $object->{$annotation->setter}($entity);
        }
    }
}