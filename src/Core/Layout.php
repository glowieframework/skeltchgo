<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\Core\ElementTrait;
    use Glowie\SkeltchGo\SkeltchGo;
    use Exception;
    use BadMethodCallException;

    /**
     * Layout core for SkeltchGo.
     * @category Layout
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) 2021
     * @license MIT
     * @link https://glowie.tk
     * @version 1.0
     */
    class Layout{
        use ElementTrait;

        /**
         * Layout view content.
         * @var string
         */
        private $_content;

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
         * Instantiates a new Layout instance.
         * @param string $layout Layout filename to instantiate.
         * @param string|null $view (Optional) View filename to parse inside the layout.
         * @param array $params (Optional) View parameters to parse.
         */
        public function __construct(string $layout, ?string $view = null, array $params = []){
            // Save original filename
            $this->_filename = $layout;
            $layout = SkeltchGo::getViewsFolder() . $layout . (!SkeltchGo::endsWith($layout, '.phtml') ? '.phtml' : '');
            if(!file_exists($layout)) throw new Exception(sprintf('Layout file "%s" not found', $this->_filename));

            // Parse parameters
            $this->_params = $params;
            $globalParams = SkeltchGo::getRenderer()->view->toArray();
            $this->__constructTrait(array_merge($globalParams, $this->_params));

            // Parse view
            if(!empty($view)){
                $view = new View($view, $this->_params, false);
                $this->_content = $view->getContent();
            }

            // Render layout
            $path = Skeltch::run($layout);
            include($path);
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
         * Renders a view file.
         * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
         * @return void
         */
        public function renderView(string $view, array $params = []){
            SkeltchGo::getRenderer()->renderView($view, array_merge($this->_params, $params));
        }

        /**
         * Renders a layout file.
         * @param string $layout Layout filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param string|null $view (Optional) View filename to render within layout. You can place its content by using `$this->getContent()`\
         * inside the layout file. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the rendered view and layout. Should be an associative array with each variable name and value.
         * @return void
         */
        public function renderLayout(string $layout, ?string $view = null, array $params = []){
            SkeltchGo::getRenderer()->renderLayout($layout, $view, array_merge($this->_params, $params));
        }

        /**
         * Returns the layout view content as string.
         * @return string View content.
         */
        public function getContent(){
            return $this->_content;
        }

    }

?>