<?php

namespace App\Packages\DynamicSeeder\DataProviders;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Exception\ParseException;

class JsonDataProvider extends DynamicSeederDataProvider
{

    protected int $flags = 0;
    protected ?bool $associative = true;

    /**
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->populateConfig($config);
    }

    /**
     * @param string $filename
     * @return $this
     * @throws ParseException If the json file is not valid
     */
    public function parseFile(string $filename): static
    {
        if (!File::exists($filename)) {
            throw new ParseException(sprintf('File "%s" does not exist.', $filename));
        }

        if (!File::isReadable($filename)) {
            throw new ParseException(sprintf('File "%s" cannot be read.', $filename));
        }

        $data = json_decode(File::get($filename), $this->associative, flags: $this->flags);

        if ($data === null) {
            throw new ParseException(sprintf('File "%s" cannot be parsed.', $filename));
        }

        $this->setData($data);

        return $this;
    }

    /**
     * @param string $input
     * @return $this
     * @throws \Exception If the json file is not valid
     */
    public function parseString(string $input): static
    {
        $data = json_decode($input, flags: $this->flags);

        if ($data === null) {
            throw new ParseException('Json string cannot be parsed.');
        }

        $this->setData($data);

        return $this;
    }
}