<?php
    namespace SkeltchGo\Core;

    use SkeltchGo\Core\ElementTrait;
    use SkeltchGo\Core\Buffer;
    use BadMethodCallException;
    use SkeltchGo\SkeltchGo;

    /**
     * View core for SkeltchGo.
     * @category View
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) 2021
     * @license MIT
     * @link https://glowie.tk
     * @version 1.0
     */
    class View{
        use ElementTrait;

        /**
         * View content.
         * @var string
         */
        private $_content;

        /**
         * View compiled file path.
         * @var string
         */
        private $_path;

        /**
         * Instantiates a new View object.
         * @param string $view View filename to instantiate.
         * @param array $params View parameters to parse.
         * @param bool $parse Immediately parse view content.
         */
        public function __construct(string $view, array $params, bool $parse){
            // Parse parameters
            $this->_path = $view;
            $viewData = SkeltchGo::getRenderer()->view->toArray();
            if(!empty($viewData)) foreach ($viewData as $key => $value) $this->{$key} = $value;
            if(!empty($params)) foreach($params as $key => $value) $this->{$key} = $value;

            // Render view
            if(SkeltchGo::getCache()) $this->_path = Skeltch::run($this->_path);
            $this->_content = $this->getBuffer();
            if($parse) echo $this->_content;
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
         * Gets a view buffer.
         * @return string The buffer contents as string.
         */
        private function getBuffer(){
            Buffer::start();
            include($this->_path);
            return Buffer::get();
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
         * Returns the view content as string.
         * @return string View content.
         */
        public function getContent(){
            return $this->_content;
        }

    }

?>