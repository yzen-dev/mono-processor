<?php

declare(strict_types=1);

namespace MonoProcessor;

/**
 * Class Breadcrumbs
 * @package MonoProcessor
 */
final class Breadcrumbs
{
    /**
     * Breadcrumbs of current execution
     * @var array<mixed>
     */
    private $breadcrumbs = [];

    /**
     * @var Breadcrumbs|null
     */
    private static $instance;

    /**
     * Get current instance
     *
     * @return Breadcrumbs
     */
    public static function getInstance(): Breadcrumbs
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Add info in breadcrumbs
     *
     * @param mixed $data
     * @return Breadcrumbs
     */
    public function add($data): Breadcrumbs
    {
        $this->breadcrumbs [] = $data;
        return $this;
    }

    /**
     * Add info for key in breadcrumbs
     *
     * @param string $key
     * @param mixed $value
     * @return Breadcrumbs
     */
    public function push(string $key, $value): Breadcrumbs
    {
        $this->breadcrumbs[$key][] = $value;
        return $this;
    }

    /**
     * Get all breadcrumbs
     *
     * @return array<mixed>
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
