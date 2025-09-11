<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Controller\Klarna;

use Klarna\Base\Api\RequestHandlerInterface;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FrontendAPIsRequestTest extends TestCase
{
    /**
     * @dataProvider prepareData
     * @param RequestHandlerInterface $controller
     * @param RequestInterface $request
     * @return void
     */
    public function testReadClassesInDirectory(RequestHandlerInterface $controller, RequestInterface $request): void
    {
        $this->assertInstanceOf(RequestHandlerInterface::class, $controller);
        $this->assertTrue(method_exists($controller, 'getRequest'));
        $this->assertEquals(get_class($request), get_class($controller->getRequest()));
    }

    public function prepareData(): array
    {
        $classes = $this->getClassesInDirectory();

        $data = [];
        foreach ($classes as $class) {
            $data[] = $this->mockControllerAndRequest($class);
        }

        return $data;
    }

    private function getClassesInDirectory(): array
    {
        $collectedClasses = [];

        $directory = __DIR__ . '/../../../../Controller/Klarna';
        // Open the directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        // Iterate through files in the directory
        foreach ($iterator as $file) {
            // Skip directories
            if ($file->isDir()) {
                continue;
            }

            // Get the PHP file's content
            $content = file_get_contents($file->getPathname());

            // Use regular expression to find classes in the file
            preg_match_all('/class\s+(\w+)/', $content, $matches);

            // Add found classes to the result array
            if (!empty($matches[1])) {
                $collectedClasses[] = $matches[1];
            }
        }

        return array_merge(...$collectedClasses);
    }

    private function mockControllerAndRequest(string $class): array
    {
        $fullClassName = sprintf('Klarna\Kp\Controller\Klarna\%s', $class);
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);
        $controller = $objectFactory->create($fullClassName);
        $dependencyMocks = $objectFactory->getDependencyMocks();

        return [
            'controller' => $controller,
            'request' => $dependencyMocks['request']
        ];
    }
}
