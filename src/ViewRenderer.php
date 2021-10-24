<?php
    namespace SkeltchGo;

    use Exception;
    use Closure;
    use SkeltchGo\Core\Element;
    use SkeltchGo\Core\View;
    use SkeltchGo\Core\Layout;

    /**
     * View renderer for SkeltchGo.
     * @category View renderer
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) 2021
     * @license MIT
     * @link https://glowie.tk
     * @version 1.0
     */
    class ViewRenderer{
        
        /**
         * Data to pass globally to views.
         * @var Element
         */
        public $view;

        /**
         * Helpers declarations.
         * @var array
         */
        public $helpers;

        /**
         * Creates a new instance of the view renderer.
         */
        public function __construct(){
            $this->helpers = [];
            $this->view = new Element();
        }

        /**
         * Renders a view file.
         * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
         * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
         * @return void
         */
        public function renderView(string $view, array $params = []){
            $view = SkeltchGo::getViewsFolder() . $view . (!SkeltchGo::endsWith($view, '.phtml') ? '.phtml' : '');
            if(file_exists($view)){
                return new View($view, $params, true);
            }else{
                throw new Exception(sprintf('View file "%s" not found', $view));
            }
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
            $layout = SkeltchGo::getViewsFolder() . $layout . (!SkeltchGo::endsWith($layout, '.phtml') ? '.phtml' : '');
            if(!empty($view)){
                $view = SkeltchGo::getViewsFolder() . $view . (!SkeltchGo::endsWith($view, '.phtml') ? '.phtml' : '');
                if (file_exists($layout)) {
                    if(file_exists($view)){
                        return new Layout($layout, $view, $params);
                    }else{
                        throw new Exception(sprintf('View file "%s" not found', $view));
                    }
                } else {
                    throw new Exception(sprintf('Layout file "%s" not found', $layout));
                }
            }else{
                if (file_exists($layout)) {
                    return new Layout($layout, '', $params);
                } else {
                    throw new Exception(sprintf('Layout file "%s" not found', $layout));
                }
            }
        }

        /**
         * Setup a view helper.
         * @param string $name Helper name.
         * @param Closure $callback Helper callback method.
         */
        public function helper(string $name, Closure $callback){
            $this->helpers[$name] = $callback;
        }

    }

?>