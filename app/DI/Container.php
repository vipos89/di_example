<?php


namespace App\DI;


use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{

    protected $entries = [];


    /**
     * @throws ContainerException
     * @throws \ReflectionException
     * @throws NotFoundException
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            $this->set($id);
        }

        if ($this->entries[$id] instanceof \Closure || is_callable($this->entries[$id])) {
            return $this->entries[$id]($this);
        }

        return $this->resolve($id);
    }


    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set($abstract, $concrete = null)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        $this->entries[$abstract] = $concrete;

    }


    /**
     * @throws ContainerException
     * @throws \ReflectionException
     * @throws NotFoundException
     */
    public function resolve($alias): object
    {

        $reflector = $this->getReflector($alias);
        $constructor = $reflector->getConstructor();

        if (!$reflector->isInstantiable()) {
            throw new ContainerException(
                "Cannot inject {$reflector->getName()} because it cannot be instantiated"
            );
        }

        if (null === $constructor) {
            return $reflector->newInstance();
        }
        $args = $this->getArguments($alias, $constructor);

        return $reflector->newInstanceArgs($args);
    }

    /**
     * @throws NotFoundException
     */
    public function getReflector($alias): ReflectionClass
    {
        $class = $this->entries[$alias];
        try {
            return (new \ReflectionClass($class));
        } catch (\ReflectionException $e) {
            throw new NotFoundException(
                $e->getMessage(), $e->getCode()
            );
        }
    }

    /**
     * @param $alias
     * @param \ReflectionMethod $constructor
     * @return array
     * @throws \ReflectionException
     */
    public function getArguments($alias, \ReflectionMethod $constructor): array
    {
        $args = [];
        $params = $constructor->getParameters();
        foreach ($params as $param) {
            if (null !== $param->getType()) {
                $args[] = $this->get(
                    $param->getType()->getName()
                );
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }
        return $args;
    }

}
