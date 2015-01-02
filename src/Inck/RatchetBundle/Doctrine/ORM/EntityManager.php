<?php

namespace Inck\RatchetBundle\Doctrine\ORM;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

class EntityManager extends EntityManagerDecorator
{
    /**
     * {@inheritdoc}
     */
    public function find($className, $id)
    {
        $this->checkConnection();
        return $this->wrapped->find($className, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object)
    {
        $this->checkConnection();
        $this->wrapped->persist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        $this->checkConnection();
        $this->wrapped->remove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        $this->checkConnection();
        return $this->wrapped->merge($object);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
    {
        $this->checkConnection();
        $this->wrapped->clear($objectName);
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object)
    {
        $this->checkConnection();
        $this->wrapped->detach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        $this->checkConnection();
        $this->wrapped->refresh($object);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->checkConnection();
        $this->wrapped->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        $this->checkConnection();
        return $this->wrapped->getRepository($className);
    }

    private function checkConnection()
    {
        if ($this->wrapped->getConnection()->ping() === false) {
            $this->wrapped->getConnection()->close();
            $this->wrapped->getConnection()->connect();
        }
    }
}
