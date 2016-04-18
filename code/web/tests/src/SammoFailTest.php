<?php
namespace Sammedia;

class SammoFailTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAuthTokenFailNoArgument()
    {

        $Sammo = new Sammo();
        $this->assertEquals(false, $Sammo->getAuthToken(''));

    }

    public function testGetAuthTokenFailNoRegistermo()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getToken'))
        ->getMock();

        $SammoMock->expects($this->once())
        ->method('getToken')
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->getAuthToken('adssadasd'));



    }

  /**
   * @dataProvider saveProvider
   */
    public function testSaveFailByParametersMissing($msisdn, $operatorid, $shortcodeid, $text, $token)
    {

        $Sam = new Sammo();
        $response = $Sam->save($msisdn, $operatorid, $shortcodeid, $text, $token);

        $this->assertContains($response, "HTTP/1.0 400 Bad Request");

    }

    public function saveProvider()
    {

        return array(
        array('asdasd', 'asdasd', 'asdasd', 'asdasd', ''),
        array('asdasd', 'asdasd', 'asdasd', '', ''),
        array('asdasd', 'asdasd', '', '', ''),
        array('asdasd', '', '', '', ''),
        array('', '', '', '', '')
        );
    }


    public function testSaveFailByNoRedis()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $SammoMock->expects($this->once())
        ->method('getRedisInstance')
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));

    }


    public function testSaveFailByNoRedisDB()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(null)
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));

    }



    public function testSaveFailByIncr()
    {
        $Sam = new Sammo();

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'incr'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('incr')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));

    }


    public function testSaveFailByhmset()
    {
        $Sam = new Sammo();

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'incr', 'hmset'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('incr')
        ->with('id')
        ->will($this->returnValue(1));

        $redisMock->expects($this->any())
        ->method('hmset')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));

    }


    public function testSaveFailByzAdd()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'incr', 'hmset', 'zAdd'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('zAdd')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));

    }

    public function testSaveFailBypersist()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'incr', 'hmset', 'zAdd', 'persist'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('zAdd')
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('persist')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->save('asdasd', 'asdasd', 'asdasd', 'asdasd', 'asdadasd'));
    }

    public function testStatFailByNoRedis()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $SammoMock->expects($this->once())
        ->method('getRedisInstance')
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->stats());
    }

    public function testStatFailByNoRedisDB()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(null)
        ->will($this->returnValue(false));

         $this->assertEquals(false, $SammoMock->stats());

    }


    public function testStatFailByzRangeByScore()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'zRangeByScore', 'hmset', 'zAdd', 'persist'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(0)
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('zRangeByScore')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->stats());

    }

    public function testStatFailBysort()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'zRangeByScore', 'sort'));

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
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('sort')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->stats());

    }

    public function testStatFailByhgetall1()
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
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('sort')
        ->with('created_at')
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('hgetall')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->stats());

    }

/*
getNotProcessedMoQty
*/

    public function testGetNotProcessedMoQtyFailByNoRedis()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $SammoMock->expects($this->once())
        ->method('getRedisInstance')
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->getNotProcessedMoQty());

    }

    public function testGetNotProcessedMoQtyFailByNoRedisDB()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(null)
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->getNotProcessedMoQty());

    }


    public function testGetNotProcessedMoQtyFailBySmembers()
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
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->getNotProcessedMoQty());
    }


/*
remNotProcessedMo
*/
    public function testRemNotProcessedMoQtyFailByNoRedis()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $SammoMock->expects($this->once())
        ->method('getRedisInstance')
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->remNotProcessedMo());

    }

    public function testRemNotProcessedMoQtyFailByNoRedisDB()
    {
    
        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->with(null)
        ->will($this->returnValue(false));

        $this->assertEquals(false, $SammoMock->remNotProcessedMo());

    }


    public function testRemNotProcessedMoQtyFailBySmembers()
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
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->remNotProcessedMo());


    }

    public function testRemNotProcessedMoQtyFailBySmembersNotArray()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'smembers', 'srem'));

        $SammoMock->expects($this->any())
        ->method('getRedisInstance')
        ->will($this->returnValue($redisMock));

        $redisMock->expects($this->any())
        ->method('select')
        ->will($this->returnValue(true));

        $redisMock->expects($this->any())
        ->method('smembers')
        ->will($this->returnValue('0'));

        $this->assertEquals(false, $SammoMock->remNotProcessedMo());
    }

    public function testRemNotProcessedMoQtyFailBySrem()
    {

        $SammoMock = $this->getMockBuilder('\Sammedia\Sammo')
        ->setMethods(array('getRedisInstance'))
        ->getMock();

        $redisMock =  $this->getMock('Predis\\Client', array('select', 'smembers', 'srem'));

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
        ->method('srem')
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->remNotProcessedMo());

    }

    public function testRemNotProcessedMoQtyFailByDel()
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
        ->will($this->returnCallback(function () {
        throw new \Predis\Response\ServerException('Generic Server Exception', 100);
        }));

        $this->assertEquals('Generic Server Exception', $SammoMock->remNotProcessedMo());

    }




}
