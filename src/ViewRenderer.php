<?php
    namespace Glowie\SkeltchGo;

    use Exception;
    use Closure;
    use Glowie\SkeltchGo\Core\Element;
    use Glowie\SkeltchGo\Core\View;
    use Glowie\SkeltchGo\Core\Layout;

    /**
     * View renderer for SkeltchGo.
     * @category View renderer
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) Glowie
     * @license MIT
     * @link https://glowie.tk
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
            return new View($view, $params);
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
            return new Layout($layout, $view, $params);
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