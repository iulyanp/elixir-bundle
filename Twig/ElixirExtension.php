<?php

namespace Iulyanp\ElixirBundle\Twig;

/**
 * Class ElixirExtension.
 */
class ElixirExtension extends \Twig_Extension
{
    /** @var string */
    protected $webDir;

    /** @var string */
    protected $buildDir;

    /**
     * ElixirExtension constructor.
     *
     * @param string $webDir
     * @param string $buildDir
     */
    public function __construct($webDir, $buildDir)
    {
        $this->webDir = $webDir;
        $this->buildDir = $buildDir;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('elixir', [$this, 'getAssetVersion']),
        ];
    }

    /**
     * @param $file
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getAssetVersion($file)
    {
        $file = trim($file, '/');

        $manifest = $this->readManifest();

        if (array_key_exists($file, $manifest)) {
            return sprintf(
                '%s%s%s%s',
                DIRECTORY_SEPARATOR,
                $this->buildDir,
                DIRECTORY_SEPARATOR,
                $manifest[$file]
            );
        }

        throw new \Exception("File {$file} not defined in asset manifest.");
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'elixir';
    }

    /**
     * @return mixed
     */
    private function readManifest()
    {
        static $manifest;
        static $manifestPath;

        $manifestFile = $this->checkManifestFileExists();

        if (is_null($manifest) || $manifestPath !== $this->buildDir) {
            $manifestPath = $this->buildDir;

            return json_decode(
                file_get_contents($manifestFile),
                true
            );
        }

        return $manifest;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    private function checkManifestFileExists()
    {
        $manifestFile = sprintf(
            '%s%s%s%s%s',
            $this->webDir,
            DIRECTORY_SEPARATOR,
            $this->buildDir,
            DIRECTORY_SEPARATOR,
            'rev-manifest.json'
        );

        if (!file_exists($manifestFile)) {
            throw new \Exception("File {$manifestFile} not defined in asset manifest.");
        }

        return $manifestFile;
    }
}
