<?php
/**
 * This file is part of Paytrail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Paytrail\Common\DataObject;

/**
 * Class DataDummy.
 */
class DataDummy extends DataObject
{
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'foo' => 1,
        );
    }
}

/**
 * Class DataObjectTest.
 */
class DataObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toJson function.
     */
    public function testToJson()
    {
        $object = new DataDummy;
        $this->assertEquals('{"foo":1}', $object->toJson());
    }
}
