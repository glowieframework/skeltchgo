<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\Core\ElementTrait;
    use Glowie\SkeltchGo\SkeltchGo;
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
         * Layout compiled file path.
         * @var string
         */
        private $_path;

        /**
         * Instantiates a new Layout object.
         * @param string $layout Layout filename to instantiate.
         * @param string $view View filename to parse inside the layout.
         * @param array $params View parameters to parse.
         */
        public function __construct(string $layout, string $view, array $params){
            // Parse parameters
            $viewData = SkeltchGo::getRenderer()->view->toArray();
            $params = array_merge($viewData, $params);
            if(!empty($params)) foreach($params as $key => $value) $this->{$key} = $value;

            // Parse view
            if(!empty($view)){
                $view = new View($view, $params, false);
                $this->_content = $view->getContent();
            }

            // Render layout
            $this->_path = Skeltch::run($layout);
            include($this->_path);
        }

        /**
         * Calls a helper method dynamically.
         * @param mixed $method Helper method to be called.
         * @param mixed $args Arguments to pass to the method.
         */
        public function __call($method, $args){
            if(!empty(SkeltchGo::getRenderer()->helpers[$method])){
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
            SkeltchGo::getRenderer()->renderView($view, $params);
        }

        /**
         * Renders a layout file.
         * @param string $layout Layout filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param string $view (Optional) View filename to render within layout. You can place its content by using `$this->getContent()`\
         * inside the layout file. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the rendered view and layout. Should be an associative array with each variable name and value.
         * @return void
         */
        public function renderLayout(string $layout, string $view = '', array $params = []){
            SkeltchGo::getRenderer()->renderLayout($layout, $view, $params);
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