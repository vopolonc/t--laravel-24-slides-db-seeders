<?php

namespace App\Packages\DynamicSeeder\DataProviders;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlDataProvider extends DynamicSeederDataProvider
{

    protected Yaml $component;
    protected int $flags = 0;

    /**
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->component = new Yaml();
        $this->populateConfig($config);
    }

    /**
     * @param string $filename
     * @return $this
     * @throws ParseException If the YAML is not valid
     */
    public function parseFile(string $filename): static
    {
        $data = Yaml::parseFile($filename, $this->flags);
        $this->setData($data);

        return $this;
    }

    /**
     * @param string $input
     * @return $this
     * @throws ParseException If the YAML is not valid
     */
    public function parseString(string $input): static
    {
        $data = Yaml::parse($input, $this->flags);
        $this->setData($data);

        return $this;
    }
}