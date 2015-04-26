<?php

use Michcald\LockIt\Adapter\MemoryAdapter;

abstract class MemoryAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Michcald\LockIt\Adapter\AdapterInterface
     */
    protected $adapter;

    public function setUp()
    {
        parent::setUp();
        $this->adapter = new MemoryAdapter();
    }

    private function clearKeys()
    {
        foreach ($this->adapter->keys() as $key) {
            $this
                ->adapter
                ->del($key)
            ;
        }
    }

    public function testSetGet()
    {
        $this->clearKeys();

        $values = array(
            array(
                'key'   => 'one',
                'value' => '1',
            ),
            array(
                'key'   => 'two',
                'value' => '',
            ),
            array(
                'key'   => '',
                'value' => 'sdf',
            ),
            array(
                'key'   => 'onetwo',
                'value' => 'sdfadad',
            ),
        );

        foreach ($values as $value) {
            $this
                ->adapter
                ->set($value['key'], $value['value'])
            ;
            $val = $this
                ->adapter
                ->get($value['key'])
            ;

            $this->assertEquals($value['value'], $val);
        }
    }

    public function testScan()
    {
        $this->clearKeys();

        $scan = $this
            ->adapter
            ->scan('one')
        ;

        $this->assertInternalType('array', $scan);

        $values = array(
            array(
                'key'   => 'one',
                'value' => '1',
            ),
            array(
                'key'   => 'two',
                'value' => '',
            ),
            array(
                'key'   => '',
                'value' => 'sdf',
            ),
            array(
                'key'   => 'onetwo',
                'value' => 'sdfadad',
            ),
        );

        foreach ($values as $value) {
            $this
                ->adapter
                ->set($value['key'], $value['value'])
            ;
        }

        $scan = $this
            ->adapter
            ->scan('one')
        ;

        $this->assertInternalType('array', $scan);
        $this->assertCount(2, $scan);
    }

    public function testExists()
    {
        $this->clearKeys();

        // @todo
    }

    public function testKeys()
    {
        $this->clearKeys();

        // @todo
    }
}