<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\SkeltchGo;
    use Exception;

    /**
     * Templating engine for SkeltchGo.
     * @category Templating engine
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) Glowie
     * @license MIT
     * @link https://glowie.tk
     */
    class Skeltch{

        /**
         * Runs Skeltch view preprocessor.
         * @param string $filename View to process.
         * @return string Returns the processed file location.
         */
        public static function run(string $filename){
            // Checks for file and cache folder permissions
            $tmpdir = SkeltchGo::getCacheFolder();
            if(!is_writable($tmpdir)) throw new Exception('Directory "' . $tmpdir . '" is not writable, please check your chmod settings');

            // Checks if cache is enabled or should be recompiled
            $tmpfile = $tmpdir . md5($filename) . '.tmp';
            if(!SkeltchGo::getCache() || !file_exists($tmpfile) || filemtime($tmpfile) < filemtime($filename)) self::compile($filename, $tmpfile);

            // Returns the processed file location
            return $tmpfile;
        }

        /**
         * Compiles a view into a temporary file.
         * @param string $filename View to compile.
         * @param string $target Target file location.
         */
        private static function compile(string $filename, string $target){
            $code = file_get_contents($filename);
            $code = self::compileEchos($code);
            $code = self::compileLoops($code);
            $code = self::compileIfs($code);
            $code = self::compileFunctions($code);
            $code = self::compilePHP($code);
            $code = self::compileComments($code);
            $code = self::compileIgnores($code);
            file_put_contents($target, $code);
        }

        /**
         * Compiles conditional statements.\
         * example: `{if($condition)}` | `{/if}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileIfs(string $code){
            $code = preg_replace('~(?<!@){\s*if\s*\((.+?)\)\s*}~is', '<?php if($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*isset\s*\((.+?)\)\s*}~is', '<?php if(isset($1)): ?>', $code);
            $code = preg_replace('~(?<!@){\s*empty\s*\((.+?)\)\s*}~is', '<?php if(SkeltchGo::isEmpty($1)): ?>', $code);
            $code = preg_replace('~(?<!@){\s*notempty\s*\((.+?)\)\s*}~is', '<?php if(!SkeltchGo::isEmpty($1)): ?>', $code);
            $code = preg_replace('~(?<!@){\s*notset\s*\((.+?)\)\s*}~is', '<?php if(!isset($1)): ?>', $code);
            $code = preg_replace('~(?<!@){\s*else\s*if\s*\((.+?)\)\s*}~is', '<?php else if($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*else\s*}~is', '<?php else: ?>', $code);
            $code = preg_replace('~(?<!@){\s*(/if|/isset|/empty|/notempty|/notset)\s*}~is', '<?php endif; ?>', $code);
            return $code;
        }

        /**
         * Compiles Glowie functions.\
         * example: `{@url('/')}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileFunctions(string $code){
            $code = preg_replace('~(?<!@){\s*view\s*\((.+?)\)\s*}~is', '<?php $this->renderView($1); ?>', $code);
            $code = preg_replace('~(?<!@){\s*layout\s*\((.+?)\)\s*}~is', '<?php $this->renderLayout($1); ?>', $code);
            $code = preg_replace('~(?<!@){\s*content\s*}~is', '<?php echo $this->getContent(); ?>', $code);
            return $code;
        }

        /**
         * Compiles raw PHP statements.\
         * example: `{% $code %}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compilePHP(string $code){
		    return preg_replace('~(?<!@){\s*%\s*(.+?)\s*%\s*}~is', '<?php $1 ?>', $code);
	    }

        /**
         * Compiles loop statements.\
         * example: `{foreach($variable as $key => $value)}` | `{/foreach}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileLoops(string $code){
            $code = preg_replace('~(?<!@){\s*foreach\s*\((.+?)\)\s*}~is', '<?php foreach($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*for\s*\((.+?)\)\s*}~is', '<?php for($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*switch\s*\((.+?)\)\s*}~is', '<?php switch($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*while\s*\((.+?)\)\s*}~is', '<?php while($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*case\s*\((.+?)\)\s*}~is', '<?php case($1): ?>', $code);
            $code = preg_replace('~(?<!@){\s*default\s*}~is', '<?php default: ?>', $code);
            $code = preg_replace('~(?<!@){\s*/foreach\s*}~is', '<?php endforeach; ?>', $code);
            $code = preg_replace('~(?<!@){\s*/for\s*}~is', '<?php endfor; ?>', $code);
            $code = preg_replace('~(?<!@){\s*/switch\s*}~is', '<?php endswitch; ?>', $code);
            $code = preg_replace('~(?<!@){\s*/while\s*}~is', '<?php endwhile; ?>', $code);
            $code = preg_replace('~(?<!@){\s*break\s*}~is', '<?php break; ?>', $code);
            $code = preg_replace('~(?<!@){\s*continue\s*}~is', '<?php continue; ?>', $code);
            return $code;
        }

        /**
         * Compiles echo statements.\
         * example: `{{ $var }}` | `{{!! $var !!}}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileEchos(string $code){
            $code = preg_replace('~(?<!@){{\s*!!\s*(.+?)\s*!!\s*}}~is', '<?php echo $1; ?>', $code);
            $code = preg_replace('~(?<!@){{\s*(.+?)\s*}}~is', '<?php echo htmlspecialchars($1); ?>', $code);
            return $code;
        }

        /**
         * Compiles comments.\
         * example: `{# this is a comment #}`
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileComments(string $code){
            return preg_replace('~(?<!@){\s*#\s*(.+?)\s*#\s*}~is', '<?php /* $1 */ ?>', $code);
        }

        /**
         * Compiles ignored statements.
         * @param string $code Code to compile.
         * @return string Returns the compiled code.
         */
        private static function compileIgnores(string $code){
            return preg_replace('~@{~is', '{', $code);
        }

    }

?>