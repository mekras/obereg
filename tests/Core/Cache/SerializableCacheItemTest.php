<?php
/**
 * PHP fault tolerance library
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Tests\Core\Cache;

use Mekras\Obereg\Core\Cache\SerializableCacheItem;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Obereg\Core\Cache\SerializableCacheItem
 *
 * @covers Mekras\Obereg\Core\Cache\SerializableCacheItem
 */
class SerializableCacheItemTest extends TestCase
{
    /**
     * Basic overall test
     */
    public function testOverall()
    {
        $original = 'TEST DATA';
        $item = new SerializableCacheItem('DUMMY');
        $item
            ->set($original)
            ->setMeta('foo', 'bar');
        $serialized = serialize($item);
        $item = unserialize($serialized);

        static::assertInstanceOf(SerializableCacheItem::class, $item);
        static::assertEquals($original, $item->get());
        static::assertEquals('bar', $item->getMeta('foo'));
    }
}
