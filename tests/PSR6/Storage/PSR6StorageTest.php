<?php
/**
 * PHP fault tolerance library.
 *
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Obereg\Tests\PSR6\Storage;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Mekras\Obereg\PSR6\Storage\PSR6Storage;

/**
 * Tests for Mekras\Obereg\PSR6\Storage\PSR6Storage.
 *
 * @covers \Mekras\Obereg\PSR6\Storage\PSR6Storage
 */
class PSR6StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test enqueue/dequeue.
     */
    public function testQueue()
    {
        $storage = new PSR6Storage(new ArrayCachePool());
        $storage->enqueue('foo', 'Foo');
        $storage->enqueue('foo', 'Bar');
        static::assertEquals('Foo', $storage->dequeue('foo')->getData());
        static::assertEquals('Bar', $storage->dequeue('foo')->getData());
    }

    /**
     * Test put/get.
     */
    public function testPutGet()
    {
        $storage = new PSR6Storage(new ArrayCachePool());
        $storage->put('foo', 'bar', 'Foo');
        $storage->put('foo', 'baz', 'Bar');
        static::assertEquals('Foo', $storage->get('foo', 'bar')->getData());
        static::assertEquals('Bar', $storage->get('foo', 'baz')->getData());
    }
}
