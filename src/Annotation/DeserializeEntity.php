<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 18/10/18
 * Time: 00:23
 */

namespace App\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class DeserializeEntity
{
    /**
     * @var string
     * @Required()
     */
    public $type; //Person
    /**
     * @var string
     * @Required()
     */
    public $idField; //id Person
    /**
     * @var string
     * @Required()
     */
    public $setter; // Role => setPerson
    /**
     * @var string
     * @Required()
     */
    public $idGetter; //Person getId
}