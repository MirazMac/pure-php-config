<?php

declare(strict_types=1);

namespace MirazMac\PurePhpConfig;

/**
 * PurePhpConfig
 *
 * A pure PHP file based no bullshit config loader with dot notation support.
 * Everything is lazy loaded, so if you don't make call to a config file it won't be loaded
 *
 * @author Miraz Mac <mirazmac@gmail.com>
 * @link https://mirazmac.com
 */
class PurePhpConfig
{
    /**
     * @var array Config storage
     */
    protected $store = [];

    /**
     * @var array Config directory location
     */
    protected $directory;

    /**
     * Create a new PurePhpConfig instance
     */
    public function __construct(string $directory)
    {
        if (!is_dir($directory)) {
            throw new \RuntimeException("No directory found at: {$directory}");
        }

        $this->directory = $directory;
    }

    /**
     * Access a config value
     *
     * @param  string $key
     * @param  mixed $fallback
     * @return mixed
     */
    public function get(string $key, $fallback = null)
    {
        extract($this->extractNamespace($key));

        // Check if cached already
        if (!isset($this->store[$namespace])) {
            $this->loadConfig($namespace, $this->directory . '/' . $namespace . '.php');
        }

        if (empty($key)) {
            return $this->store[$namespace];
        }

        return DotArrayAccessor::get($this->store[$namespace], $key, $fallback);
    }

    /**
     * Set a config value, for runtime only doesn't replace the actual file
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        extract($this->extractNamespace($key));

        // Check if cached already
        if (!isset($this->store[$namespace])) {
            $this->loadConfig($namespace, $this->directory . '/' . $namespace . '.php');
        }

        // Set an entire namespace's value
        if (empty($key)) {
            if (!is_array($value)) {
                throw new \InvalidArgumentException("Attempting to replace a namespace's data, but provided value isn't array.");
            }

            // Replace the entire array
            $this->store[$namespace] = $value;
            return true;
        }


        return DotArrayAccessor::set($this->store[$namespace], $key, $value);
    }

    /**
     * Check if a key exists or not
     *
     * @param  string $key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        extract($this->extractNamespace($key));
        
        // Check if cached already
        if (!isset($this->store[$namespace])) {
            $this->loadConfig($namespace, $this->directory . '/' . $namespace . '.php');
        }

        // If key is empty then we determine if the namespace exists or not
        if (empty($key)) {
            return true;
        }

        return DotArrayAccessor::exists($this->store[$namespace], $key);
    }

    /**
     * Delete a key value
     *
     * @param  string $key
     * @return bool
     */
    public function delete(string $key) : bool
    {
        extract($this->extractNamespace($key));
        
        // Check if cached already
        if (!isset($this->store[$namespace])) {
            $this->loadConfig($namespace, $this->directory . '/' . $namespace . '.php');
        }

        // Delete the key instead
        if (empty($key)) {
            $this->store[$namespace] = [];
            return true;
        }

        return DotArrayAccessor::delete($this->store[$namespace], $key);
    }

    /**
     * Returns all loaded configs
     *
     * @return array
     */
    public function getAll() : array
    {
        return $this->store;
    }

    /**
     * Extracts the "namespace" from the key
     *
     * @param  string $key
     * @return array
     */
    protected function extractNamespace(string $key) : array
    {
        $parts = explode('.', trim($key));
        $namespace = $parts[0];

        unset($parts[0]);

        $key = join('.', $parts);

        return ['key' => $key, 'namespace' => $namespace];
    }

    /**
     * Loads config array from a file
     *
     * @param  string $namespace
     * @param  string $file
     * @return bool
     *
     * @throws \RuntimeException If the config file doesn't exist
     * @throws \RuntimeException If the config file doesn't return a valid array
     */
    protected function loadConfig(string $namespace, string $file) : bool
    {
        if (!is_file($file)) {
            throw new \RuntimeException("No config file exists at: {$file}");
        }
        
        $config = include $file;

        if (!is_array($config)) {
            throw new \RuntimeException("Config file at: {$file} doesn't return a PHP array");
        }

        $this->store[$namespace] = $config;

        return true;
    }
}
