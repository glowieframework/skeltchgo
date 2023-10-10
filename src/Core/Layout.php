<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\Core\ElementTrait;
    use Glowie\SkeltchGo\SkeltchGo;
    use Exception;
    use BadMethodCallException;
    use JsonSerializable;

    /**
     * Layout core for SkeltchGo.
     * @category Layout
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) Glowie
     * @license MIT
     * @link https://eugabrielsilva.tk/glowie
     */
    class Layout implements JsonSerializable{
        use ElementTrait;

        /**
         * Layout view content.
         * @var string
         */
        private $_content;

        /**
         * Internal view content
         * @var string
         */
        private $_view = '';

        /**
         * Layout original filename.
         * @var string
         */
        private $_filename;

        /**
         * Layout local parameters.
         * @var array
         */
        private $_params;

        /**
         * Instantiates a new Layout.
         * @param string $layout Layout filename to instantiate.
         * @param string|null $view (Optional) View filename to parse inside the layout.
         * @param array $params (Optional) View parameters to parse.
         */
        public function __construct(string $layout, ?string $view = null, array $params = []){
            // Save original filename
            $this->_filename = $layout;
            $layout = SkeltchGo::getViewsFolder() . $layout . (!SkeltchGo::endsWith($layout, '.phtml') ? '.phtml' : '');
            if(!is_file($layout)) throw new Exception(sprintf('Layout file "%s" not found', $this->_filename));

            // Parse parameters
            $this->_params = $params;
            $globalParams = SkeltchGo::getRenderer()->view->toArray();
            $this->__constructTrait(array_merge($globalParams, $this->_params));

            // Parse view
            if(!empty($view)){
                $view = new View($view, $this->_params);
                $this->_view = $view->getContent();
            }

            // Render layout
            $layout = Skeltch::run($layout);
            $this->_content = $this->getBuffer($layout);
        }

        /**
         * Calls a helper method dynamically.
         * @param mixed $method Helper method to be called.
         * @param mixed $args Arguments to pass to the method.
         */
        public function __call($method, $args){
            if(!empty(SkeltchGo::getRenderer()->helpers[$method]) && is_callable(SkeltchGo::getRenderer()->helpers[$method])){
                return call_user_func_array(SkeltchGo::getRenderer()->helpers[$method], $args);
            }else{
                throw new BadMethodCallException('Helper method "' . $method .'()" is not defined');
            }
        }

        /**
         * Gets a layout buffer.
         * @param string $path Layout filename to include.
         * @return string The buffer contents as string.
         */
        private function getBuffer(string $path){
            ob_start();
            include($path);
            return ob_get_clean();
        }

        /**
         * Renders a view file.
         * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
         */
        public function renderView(string $view, array $params = []){
            SkeltchGo::getRenderer()->renderView($view, array_merge($this->_params, $params));
        }

        /**
         * Renders a layout file.
         * @param string $layout Layout filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param string|null $view (Optional) View filename to render within layout. You can place its content by using `$this->getView()`\
         * inside the layout file. Must be a **.phtml** file inside **app/views** folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the rendered view and layout. Should be an associative array with each variable name and value.
         */
        public function renderLayout(string $layout, ?string $view = null, array $params = []){
            SkeltchGo::getRenderer()->renderLayout($layout, $view, array_merge($this->_params, $params));
        }

        /**
         * Renders a view file in a private scope. No global or parent view properties will be inherited.
         * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
         */
        public function renderPartial(string $view, array $params = []){
            SkeltchGo::getRenderer()->renderPartial($view, $params);
        }

        /**
         * Returns the layout content as string.
         * @return string Layout content.
         */
        public function getContent(){
            return $this->_content;
        }

        /**
         * Returns the internal view content as string.
         * @return string View content.
         */
        public function getView(){
            return $this->_view;
        }

        /**
         * Starts a layout block.
         * @param string $name Block name.
         */
        public static function startBlock(string $name){
            View::startBlock($name);
        }

        /**
         * Finishes a layout block.
         */
        public static function endBlock(){
            View::endBlock();
        }

        /**
         * Gets a block content.
         * @param string $name Block name.
         * @param string $default (Optional) Default content to return.
         * @return string Returns the block content or the default if block is not found.
         */
        public static function getBlock(string $name, string $default = ''){
            return View::getBlock($name, $default);
        }

    }

?>