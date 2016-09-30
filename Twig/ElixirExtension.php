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
     * @param $asset
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getAssetVersion($asset)
    {
        $asset = trim($asset, '/');

        $manifest = $this->readManifest();

        if (array_key_exists($asset, $manifest)) {
            return sprintf(
                '%s%s%s%s',
                DIRECTORY_SEPARATOR,
                $this->buildDir,
                DIRECTORY_SEPARATOR,
                $manifest[$asset]
            );
        }

        throw new \Exception(sprintf('File %s not defined in asset manifest.', $asset));
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
            throw new \Exception(sprintf('File %s not defined in asset manifest.', $manifestFile));
        }

        return $manifestFile;
    }
}
