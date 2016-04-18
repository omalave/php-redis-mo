<?php
/**
 * The file holds all functions for MO Processing
 *
 * PHP version 5
 *
 * @category  Backend
 * @package   SamMo
 * @author    Omar Malave <omalave@gmail.com>
 * @copyright 2015 Omar Malave
 * @license   SamMedia https://bitbucket.org/sammediatalenttest/omalave
 * @link      https://bitbucket.org/sammediatalenttest/omalave
 */
namespace Sammedia;

/**
 * Sammo
 *
 * @category  Backend
 * @package   SamMo
 * @author    Omar Malave <omalave@gmail.com>
 * @copyright 2015 Omar Malave
 * @license   SamMedia https://bitbucket.org/sammediatalenttest/omalave
 * @link      https://bitbucket.org/sammediatalenttest/omalave
 */
class Sammo
{

    /**
     * GetAuthToken
     *
     * @param string $REQUEST URL Parameters
     *
     * @return string
     */
    public function getAuthToken($REQUEST)
    {

        if (empty($REQUEST)) {
            return false;
        }

        $arg   = json_encode($REQUEST);
        $token = $this->getToken($arg);

        if (!$token || empty($token)) {
            return false;
        }

        return $token;

    }

    /**
     * Save
     *
     * @param string $msisdn URL Parameters
     * @param string $operatorid URL Parameters
     * @param string $shortcodeid URL Parameters
     * @param string $text URL Subscription request
     * @param string $token URL Parameters
     *
     * Save MO details to redis
     *
     * @return boolean
     */
    public function save($msisdn, $operatorid, $shortcodeid, $text, $token)
    {
        if ((!isset($msisdn) || empty($msisdn)) || (!isset($msisdn) || empty($operatorid)) || (!isset($msisdn) || empty($shortcodeid)) || (!isset($msisdn) || empty($text)) || (!isset($msisdn) || empty($token))) {

            return "HTTP/1.0 400 Bad Request";
            die();
        }

        $date  = date('Y-m-d H:i:s');
        $tdate = strtotime($date);
        $redis = $this->getRedisInstance();

        if (!$redis) {
              return false;
        }

        if (!$redis->select(0)) {
              return false;
        }

        try {
              $id = $redis->incr('id');
              $set = 'mo:'.$id;

              $redis->hmset($set, [
                  'id'          => $id,
                  'msisdn'      => $msisdn,
                  'operatorid'  => $operatorid,
                  'shortcodeid' => $shortcodeid,
                  'text'        => $text,
                  'token'       => $token,
                  'date'        => $date
              ]);

              $redis->zAdd('created_at', $tdate, $id);
              $redis->zAdd('mo_id', $id, $id);
              $redis->zAdd('"processed"', 0, $id);
              $redis->persist($id);

              return "{'status': 'ok'}";

        } catch (\Predis\Response\ServerException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Save
     *
     * Get MO stats from redis
     *
     * @return string
     */
    public function stats()
    {

        $response = array();
        $t15m_ago = new \DateTime("15 minutes ago");
        $s        = strtotime($t15m_ago->format("Y-m-d H:i:s"));
        $now      =  strtotime(date('Y-m-d H:i:s'));

        $redis = $this->getRedisInstance();

        if (!$redis) {
            return false;
        }

        if (!$redis->select(0)) {
            return false;
        }

        try {
            $response['last_15_min_mo_count'] = count($redis->zRangeByScore('created_at', $s, $now));

            $tSpan = $redis->sort('created_at', array(
              'by' => 'created_at',
              'sort' => 'DESC',
              'limit' => array(0, 10000),
              ));

            $n1Item   = 'mo:'.$tSpan[0];
            $lastItem = 'mo:'.count($tSpan);

            $dateMinMax[] = $redis->hgetall($n1Item)['date'];
            $dateMinMax[] = $redis->hgetall($lastItem)['date'];

            $response['time_span_last_10k'] = $dateMinMax;

            return json_encode($response);
        } catch (\Predis\Response\ServerException $e) {
            return $e->getMessage();
        }

    }

    /**
     * GetNotProcessedQty
     *
     * Get not processed MO qty
     *
     * @return string
     */
    public function getNotProcessedMoQty()
    {

        $redis = $this->getRedisInstance();

        if (!$redis) {
            return false;
        }

        if (!$redis->select(0)) {
            return false;
        }

        try {

            $notProcessed = $redis->smembers("notprocessed");
            return count($notProcessed);

        } catch (\Predis\Response\ServerException $e) {
            return $e->getMessage();
        }

    }

    /**
     * RemNotProcessed
     *
     * Remove not processed MO from DB
     *
     * @return string
     */
    public function remNotProcessedMo()
    {

        $redis = $this->getRedisInstance();

        if (!$redis) {
            return false;
        }

        if (!$redis->select(0)) {
            return false;
        }

        try {

            $notProcessed = $redis->smembers("notprocessed");

            if (!is_array($notProcessed)) {
                return false;
            }

            foreach ($notProcessed as $item) {

                $id = "mo:".$item;
                $redis->srem('notprocessed', $item);
                $redis->del($id);
            }

            return "Ok";

        } catch (\Predis\Response\ServerException $e) {
            return $e->getMessage();
        }

    }


    /**
     * GetRedisInstance
     *
     * This function is for unit testing porpouses
     *
     * @codeCoverageIgnore
     *
     * @return object
     */
    public function getRedisInstance()
    {

        return new \Predis\Client();

    }

    /**
     * GetToken
     *
     * This function retrieve a token from registermo API
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getToken($arg)
    {

        if (!file_exists('./var/registermo')) {
            return false;
        }

        $token = `./var/registermo $arg`;
        return $token;

    }
}
