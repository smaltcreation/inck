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
        return $this->execute(function() use ($className, $id) {
            return $this->wrapped->find($className, $id);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object)
    {
        $this->execute(function() use ($object) {
            $this->wrapped->persist($object);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        $this->execute(function() use ($object) {
            $this->wrapped->remove($object);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        return $this->execute(function() use ($object) {
            return $this->wrapped->merge($object);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
    {
        $this->execute(function() use ($objectName) {
            $this->wrapped->clear($objectName);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object)
    {
        $this->execute(function() use ($object) {
            $this->wrapped->detach($object);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        $this->execute(function() use ($object) {
            $this->wrapped->refresh($object);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->execute(function() {
            $this->wrapped->flush();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className)
    {
        return $this->execute(function() use ($className) {
            return $this->wrapped->getRepository($className);
        });
    }

    private function execute($callback)
    {
        $this->reconnect();
        return $callback();
    }

    private function reconnect() {
        if ($this->wrapped->getConnection()->ping() === false) {
            $this->wrapped->getConnection()->close();
            $this->wrapped->getConnection()->connect();
        }
    }
}
