<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Quote;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Quote
 */
class QuoteTest extends TestCase
{
    /**
     * @var CartInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mageQuoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var QuoteInterface
     */
    protected $quote;

    /**
     * @var \Magento\Framework\DataObject\Factory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactoryMock;

    /**
     * @covers \Klarna\Kp\Model\Quote::isActive()
     * @covers \Klarna\Kp\Model\Quote::setIsActive()
     */
    public function testIsActiveAccessors()
    {
        $value = 1;
        $key = 'is_active';

        $this->quote
            ->expects($this->once())
            ->method('setData')
            ->with($key);

        $this->quote->expects($this->once())
            ->method('_getData')
            ->with($key)
            ->willReturn($value);

        $this->quote->setIsActive($value);
        $result = $this->quote->isActive();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers \Klarna\Kp\Model\Quote::getClientToken()
     * @covers \Klarna\Kp\Model\Quote::setClientToken()
     */
    public function testClientTokenAccessors()
    {
        $value = 'KLARNA-CLIENT-TOKEN';
        $key = 'client_token';

        $this->quote
            ->expects($this->once())
            ->method('setData')
            ->with($key);

        $this->quote
            ->expects($this->once())
            ->method('_getData')
            ->with($key)
            ->willReturn($value);

        $this->quote->setClientToken($value);
        $result = $this->quote->getClientToken();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers \Klarna\Kp\Model\Quote::getSessionId()
     * @covers \Klarna\Kp\Model\Quote::setSessionId()
     */
    public function testSessionIdAccessors()
    {
        $value = 'klarna-session-id';
        $key = 'session_id';

        $this->quote
            ->expects($this->once())
            ->method('setData')
            ->with($key);

        $this->quote
            ->expects($this->once())
            ->method('_getData')
            ->with($key)
            ->willReturn($value);

        $this->quote->setSessionId($value);
        $result = $this->quote->getSessionId();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers \Klarna\Kp\Model\Quote::getIdentities()
     */
    public function testGetIdentities()
    {
        $value = 1;

        $this->quote
            ->expects($this->once())
            ->method('getId')
            ->willReturn($value);

        $result = $this->quote->getIdentities();

        $this->assertEquals([Quote::CACHE_TAG . '_' . $value], $result);
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setData', '_getData', 'getIdFieldName', 'getId'])
            ->getMock();

        $this->quote->method('getIdFieldName')
            ->willReturn('quote_id');
    }
}
