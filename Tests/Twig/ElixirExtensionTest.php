<?php

namespace Iulyanp\ElixirBundle\Tests\Twig;

use Iulyanp\ElixirBundle\Twig\ElixirExtension;

/**
 * Class ElixirExtensionTest.
 */
class ElixirExtensionTest extends \PHPUnit_Framework_TestCase
{
    const WEB_DIR = __DIR__;
    const BUILD_DIR = 'stub';

    /**
     * @var
     */
    private $elixirExtension;

    /**
     * Set UP.
     *
     * Instantiate ElixirExtension for all tests
     */
    public function setUp()
    {
        $this->elixirExtension = new ElixirExtension(self::WEB_DIR, self::BUILD_DIR);
    }

    /**
     * @test
     */
    public function get_asset_version_function_return_manifest()
    {
        $this->assertEquals(
            sprintf('%s%s%s%s', DIRECTORY_SEPARATOR, self::BUILD_DIR, DIRECTORY_SEPARATOR, 'css/app-db9165hf67.css'),
            $this->elixirExtension->getAssetVersion('css/app.css')
        );

        $this->assertEquals(
            sprintf('%s%s%s%s', DIRECTORY_SEPARATOR, self::BUILD_DIR, DIRECTORY_SEPARATOR, 'css/index-9rt53c9u67.css'),
            $this->elixirExtension->getAssetVersion('css/index.css')
        );

        $this->assertEquals(
            sprintf('%s%s%s%s', DIRECTORY_SEPARATOR, self::BUILD_DIR, DIRECTORY_SEPARATOR, 'js/app-db9183c967.js'),
            $this->elixirExtension->getAssetVersion('js/app.js')
        );
    }

    /**
     * @test
     */
    public function elixir_function_throws_error_when_file_not_exists()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('File css/not_existing.css not defined in asset manifest.');
        $this->elixirExtension->getAssetVersion('css/not_existing.css');
    }
}
