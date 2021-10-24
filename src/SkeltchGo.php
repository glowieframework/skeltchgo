<?php
    namespace SkeltchGo;

    /**
     * Standalone Skeltch templating engine.
     * @category Templating engine
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) 2021
     * @license MIT
     * @link https://glowie.tk
     * @version 1.0
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
         * Starts SkeltchGo module.
         * @param string $viewsFolder (Optional) Folder where the view files are stored, relative to the running script.
         * @param bool $cache (Optional) Enable views caching. Highly recommended in a production environment.
         * @param string $cacheFolder (Optional) View cache folder, relative to the running script. **Must have writing permissions.**
         * @return ViewRenderer Returns an instance of the view renderer.
         */
        public static function make(string $viewsFolder = 'views', bool $cache = true, string $cacheFolder = 'cache'){
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

    }

?>