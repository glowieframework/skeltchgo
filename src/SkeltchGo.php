<?php
    namespace Glowie\SkeltchGo;

    use Countable;

    /**
     * Standalone Skeltch templating engine.
     * @category Templating engine
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) Glowie
     * @license MIT
     * @link https://glowie.tk
     */
    class SkeltchGo{

        /**
         * Views caching enabled.
         * @var bool
         */
        private static $cache;

        /**
         * View cache folder.
         * @var string
         */
        private static $cacheFolder;

        /**
         * View renderer instance.
         * @var ViewRenderer
         */
        private static $renderer;

        /**
         * Views folder.
         * @var string
         */
        private static $viewsFolder;

        /**
         * Disable the constructor instance.
         */
        private function __construct(){}

        /**
         * Creates a SkeltchGo instance.
         * @param string $viewsFolder (Optional) Folder where the view files are stored, relative to the running script.
         * @param string $cacheFolder (Optional) View cache folder, relative to the running script. **Must have writing permissions.**
         * @param bool $cache (Optional) Enable views caching. Highly recommended in a production environment.
         * @return ViewRenderer Returns an instance of the view renderer.
         */
        public static function make(string $viewsFolder = 'views', string $cacheFolder = 'cache', bool $cache = true){
            self::$cache = $cache;
            self::$renderer = new ViewRenderer();
            self::$cacheFolder = $cacheFolder . (!self::endsWith($cacheFolder, '/') ? '/' : '');
            self::$viewsFolder = $viewsFolder . (!self::endsWith($viewsFolder, '/') ? '/' : '');
            return self::$renderer;
        }

        /**
         * Returns if views caching is enabled.
         * @return bool Caching enabled.
         */
        public static function getCache(){
            return self::$cache;
        }

        /**
         * Returns the views cache folder.
         * @return string Cache folder.
         */
        public static function getCacheFolder(){
            return self::$cacheFolder;
        }

        /**
         * Returns the view renderer instance.
         * @return ViewRenderer View renderer instance.
         */
        public static function getRenderer(){
            return self::$renderer;
        }

        /**
         * Returns the views folder.
         * @return string Views folder.
         */
        public static function getViewsFolder(){
            return self::$viewsFolder;
        }

        /**
         * Checks if a string ends with a given substring.
         * @param string $haystack The string to search in.
         * @param string $needle The substring to search for in the haystack.
         * @return bool Returns **true** if haystack ends with needle, **false** otherwise.
         */
        public static function endsWith(string $haystack, string $needle){
            $length = strlen($needle);
            if (!$length) return true;
            return substr($haystack, -$length) == $needle;
        }

        /**
         * Returns a value from a multi-dimensional array in dot notation.
         * @param array $array Array to get the value.
         * @param mixed $key Key to get in dot notation.
         * @param mixed $default (Optional) Default value to return if the key does not exist.
         * @return mixed Returns the value if exists or the default if not.
         */
        public static function arrayGet(array $array, $key, $default = null){
            // Checks if the key does not exist already
            if(isset($array[$key])) return $array[$key];

            // Loops through each key
            foreach(explode('.', $key) as $segment){
                if(!is_array($array) || !isset($array[$segment])) return $default;
                $array = $array[$segment];
            }

            // Returns the value
            return $array;
        }

        /**
         * Sets a value to a key in a multi-dimensional array using dot notation.
         * @param array $array Array to set the value.
         * @param mixed $key Key to set in dot notation.
         * @param mixed $value Value to set.
         */
        public static function arraySet(array &$array, $key, $value){
            $item = &$array;
            foreach(explode('.', $key) as $segment){
                if(isset($item[$segment]) && !is_array($item[$segment])) $item[$segment] = [];
                $item = &$item[$segment];
            }
            $item = $value;
        }

        /**
         * Checks if a variable is empty.\
         * A numeric/bool safe version of PHP `empty()` function.
         * @var mixed $variable Variable to be checked.
         * @return bool Returns true if the variable is empty, false otherwise.
         */
        public static function isEmpty($variable){
            if(!isset($variable)) return true;
            if(is_string($variable)) return trim($variable) === '';
            if(is_numeric($variable) || is_bool($variable)) return false;
            if($variable instanceof Countable) return count($variable) === 0;
            return empty($variable);
        }

    }

?>