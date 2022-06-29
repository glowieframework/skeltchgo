<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\Core\ElementTrait;
    use Glowie\SkeltchGo\SkeltchGo;
    use Exception;
    use BadMethodCallException;

    /**
     * View core for SkeltchGo.
     * @category View
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) Glowie
     * @license MIT
     * @link https://glowie.tk
     */
    class View{
        use ElementTrait;

        /**
         * View content.
         * @var string
         */
        private $_content;

        /**
         * View original filename.
         * @var string
         */
        private $_filename;

        /**
         * View local parameters.
         * @var array
         */
        private $_params;

        /**
         * Instantiates a new View instance.
         * @param string $view View filename to instantiate.
         * @param array $params (Optional) View parameters to parse.
         * @param bool $parse (Optional) Immediately parse view content.
         */
        public function __construct(string $view, array $params = [], bool $parse = true){
            // Validate file
            $this->_filename = $view;
            $view = SkeltchGo::getViewsFolder() . $view . (!SkeltchGo::endsWith($view, '.phtml') ? '.phtml' : '');
            if(!file_exists($view)) throw new Exception(sprintf('View file "%s" not found', $this->_filename));

            // Parse parameters
            $this->_params = $params;
            $globalParams = SkeltchGo::getRenderer()->view->toArray();
            $this->__constructTrait(array_merge($globalParams, $this->_params));

            // Render view
            $path = Skeltch::run($view);
            $this->_content = $this->getBuffer($path);
            if($parse) echo $this->_content;
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
         * Gets a view buffer.
         * @param string $path View filename to include.
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
         * Returns the view content as string.
         * @return string View content.
         */
        public function getContent(){
            return $this->_content;
        }

    }

?>