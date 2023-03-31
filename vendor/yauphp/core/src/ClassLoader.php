<?php
namespace Yauphp\Core;

/**
 * 自动加载类
 * @author Tomix
 *
 */
class ClassLoader
{
    /**
     * 命名空间前缀与目录映射
     * @var array
     */
    private static $m_classMap=[];

    /**
     *自动加载主函数
     * @param string $class 类全名称
     */
    public static function _autoLoad($class){

        $path="";
        foreach (self::$m_classMap as $namespace=>$dir){
            if(!empty($namespace) && strpos($class, $namespace)===0){
                $namespace=rtrim($namespace,"\\");
                $class=substr($class, strlen($namespace));
                $path = rtrim($dir,"/").str_replace("\\", "/", $class) . ".php";
                break;
            }
        }
        if(file_exists($path)){
            require_once $path;
        }
    }

    /**
     * 载入加载器
     * @param array $map
     */
    public static function load($map=[]){

        if(!empty($map)){
            self::$m_classMap=$map;
            spl_autoload_register(["Yauphp\\Core\\ClassLoader", "_autoLoad"]);
        }
    }
}
