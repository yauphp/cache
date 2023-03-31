<?php
namespace Yauphp\Cache\Impl;

use Predis\Client;
use Yauphp\Cache\ICacher;
use Yauphp\Common\IO\File;

/**
 * Predis缓存类
 * @author Tomix
 *
 */
class PredisCacher implements ICacher
{
    protected $host="127.0.0.1";
    protected $port=6379;
    protected $database=15;
    protected $prefix="";

    /**
     * @var Client
     */
    private $m_client;
    

    /**
     * 读取缓存
     * {@inheritDoc}
     * @see \swiftphp\core\cache\ICacher::get()
     */
    public function get($key){
        if($this->getClient()->exists($key)){
            $value=$this->getClient()->get($key);
            return unserialize($value);
        }
        return null;
    }

    /**
     * 写入缓存
     * {@inheritDoc}
     * @see \swiftphp\core\cache\ICacher::set()
     */
    public function set($key,$value){
        $this->getClient()->set($key,serialize($value));
        $this->getClient()->set($key.":time",time());
    }

    /**
     * 移除缓存
     * {@inheritDoc}
     * @see \swiftphp\core\cache\ICacher::remove()
     */
    public function remove($key){
        if($this->getClient()->exists($key)){
            $this->getClient()->del($key);
        }
        if($this->getClient()->exists($key.":time")){
            $this->getClient()->del($key.":time");
        }
    }

    /**
     * 清空缓存
     * {@inheritDoc}
     * @see \swiftphp\core\cache\ICacher::clear()
     */
    public function clear(){
        $client=$this->getClient();
        $keys = $client->keys("*");
        foreach ($keys as $key) {
            if(!empty($this->prefix)){
                $key = substr($key, strlen($this->prefix));
            }
            $client->del($key);
            $client->del($key.":time");             
        }
    }

    /**
     * 获取缓存时间
     * {@inheritDoc}
     * @see \swiftphp\core\cache\ICacher::getCacheTime()
     */
    public function getCacheTime($key){
        if($this->getClient()->exists($key.":time")){
            $val = $this->getClient()->get($key.":time");
            return intval($val);
        }
        return -1;
    }


    /**
     * @return Client
     */
    protected function getClient() : Client{

        if($this->m_client!=null){
            return $this->m_client;
        }
        $parameters = [
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
        ];
        $options = ['prefix' => $this->prefix,
            //'serialize'=>$this->'json',
        ];

        $this->m_client=new Client($parameters,$options);
        return $this->m_client;
    }



    /**
     * Set the value of host
     *
     * @return  self
     */ 
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set the value of port
     *
     * @return  self
     */ 
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Set the value of database
     *
     * @return  self
     */ 
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Set the value of prefix
     *
     * @return  self
     */ 
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}