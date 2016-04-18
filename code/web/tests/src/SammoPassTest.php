<?php
namespace Sammedia;

class SammoPassTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAuthTokenPass()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
                      ->setMethods(array('getToken'))
                      ->getMock();

        $SammoMock->method('getToken')
              ->willReturn('adsadadasd');

        $this->assertEquals('adsadadasd', $SammoMock->getAuthToken('adssadasd'));

    }

    public function testSavePass()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('\Predis', array('Client', 'select', 'incr', 'hmset', 'zAdd', 'persist'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('incr')
        ->will($this->returnValue(12));

        $redisMock->expects($this->any())
        ->method('hmset')
        ->will($this->returnValue('dasd'));

        $redisMock->expects($this->any())
        ->method('zAdd')
        ->will($this->returnValue('dasd'));

        $redisMock->expects($this->any())
        ->method('persist')
        ->will($this->returnValue(true));

        $this->assertEquals("{'status': 'ok'}", $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'dawdadaw'));

    }


    public function testStatPass()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'zRangeByScore', 'sort', 'hgetall'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('zRangeByScore')
        ->with('created_at')
        ->will($this->returnValue(0));

        $redisMock->expects($this->any())
        ->method('sort')
        ->with('created_at')
        ->will($this->returnValue(0));

        $redisMock->expects($this->any())
        ->method('hgetall')
        ->will($this->returnValue(0));
        
        $response = $SammoMock->stats();
        $this->assertEquals('{"last_15_min_mo_count":1,"time_span_last_10k":[null,null]}', $response);

    }


    public function testRemNotProcessedMoQtyPass()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'smembers', 'srem', 'del'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $testData =  array(3,4,5,6);

        $redisMock->expects($this->any())
        ->method('smembers')
        ->with('notprocessed')
        ->will($this->returnValue($testData));

        $redisMock->expects($this->any())
        ->method('smembers')
        ->with('notprocessed')
        ->will($this->returnValue($testData));

        $redisMock->expects($this->any())
        ->method('srem')
        ->will($this->returnValue(3));

        $redisMock->expects($this->any())
        ->method('del')
        ->will($this->returnValue(true));

        $this->assertEquals('Ok', $SammoMock->remNotProcessedMo());

    }


    public function testGetNotProcessedMoQtyPass()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'smembers'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('smembers')
        ->will($this->returnValue(array(2,3,4,5)));

        $this->assertEquals(4, $SammoMock->getNotProcessedMoQty());


    }



}
