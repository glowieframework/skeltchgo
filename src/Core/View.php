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
 * @link https://eugabrielsilva.tk/glowie
 */
class View
{
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
     * View blocks.
     * @var array
     */
    private static $_blocks;

    /**
     * Current block name.
     * @var string
     */
    private static $_block;

    /**
     * View stacks.
     * @var array
     */
    private static $_stacks;

    /**
     * Current stack name.
     * @var string
     */
    private static $_stack;

    /**
     * If stack should be prepended instead of pushed to end.
     * @var bool
     */
    private static $_prependStack = false;

    /**
     * Instantiates a new View.
     * @param string $view View filename to instantiate.
     * @param array $params (Optional) View parameters to parse.
     * @param bool $partial (Optional) Restrict view partial scope.
     */
    public function __construct(string $view, array $params = [], bool $partial = false)
    {
        // Validate file
        $this->_filename = $view;
        $view = SkeltchGo::getViewsFolder() . $view . (!SkeltchGo::endsWith($view, '.phtml') ? '.phtml' : '');
        if (!is_file($view)) throw new Exception(sprintf('View file "%s" not found', $this->_filename));

        // Parse parameters
        $this->_params = $params;
        $globalParams = !$partial ? SkeltchGo::getRenderer()->view->toArray() : [];
        $this->__constructTrait(array_merge($globalParams, $this->_params));

        // Render view
        $path = Skeltch::run($view);
        $this->_content = $this->getBuffer($path);
    }

    /**
     * Calls a helper method dynamically.
     * @param mixed $method Helper method to be called.
     * @param mixed $args Arguments to pass to the method.
     */
    public function __call($method, $args)
    {
        if (!empty(SkeltchGo::getRenderer()->helpers[$method]) && is_callable(SkeltchGo::getRenderer()->helpers[$method])) {
            return call_user_func_array(SkeltchGo::getRenderer()->helpers[$method], $args);
        } else {
            throw new BadMethodCallException('Helper method "' . $method . '()" is not defined');
        }
    }

    /**
     * Gets a view buffer.
     * @param string $path View filename to include.
     * @return string The buffer contents as string.
     */
    private function getBuffer(string $path)
    {
        ob_start();
        include($path);
        return ob_get_clean();
    }

    /**
     * Renders a view file.
     * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
     * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
     */
    public function renderView(string $view, array $params = [])
    {
        SkeltchGo::getRenderer()->renderView($view, array_merge($this->_params, $params));
    }

    /**
     * Renders a layout file.
     * @param string $layout Layout filename. Must be a **.phtml** file inside the views folder, extension is not needed.
     * @param string|null $view (Optional) View filename to render within layout. You can place its content by using `$this->getView()`\
     * inside the layout file. Must be a **.phtml** file inside the views folder, extension is not needed.
     * @param array $params (Optional) Parameters to pass into the rendered view and layout. Should be an associative array with each variable name and value.
     */
    public function renderLayout(string $layout, ?string $view = null, array $params = [])
    {
        SkeltchGo::getRenderer()->renderLayout($layout, $view, array_merge($this->_params, $params));
    }

    /**
     * Renders a view file in a private scope. No global or parent view properties will be inherited.
     * @param string $view View filename. Must be a **.phtml** file inside the views folder, extension is not needed.
     * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
     */
    public function renderPartial(string $view, array $params = [])
    {
        SkeltchGo::getRenderer()->renderPartial($view, $params);
    }

    /**
     * Renders a raw view code using Skeltch engine.
     * @param string $view View content in HTML.
     * @param array $params (Optional) Parameters to pass into the view. Should be an associative array with each variable name and value.
     */
    final public function renderInline(string $content, array $params = [])
    {
        SkeltchGo::getRenderer()->renderInline($content, $params);
    }

    /**
     * Returns the view content as string.
     * @return string View content.
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Starts a layout block.
     * @param string $name Block name.
     */
    public static function startBlock(string $name)
    {
        if (self::$_block) throw new Exception('startBlock(): Block is already started');
        self::$_block = $name;
        ob_start();
    }

    /**
     * Finishes a layout block.
     */
    public static function endBlock()
    {
        if (!self::$_block) throw new Exception('endBlock(): No block was started');
        self::$_blocks[self::$_block] = ob_get_clean();
        self::$_block = null;
    }

    /**
     * Gets a block content.
     * @param string $name Block name.
     * @param string $default (Optional) Default content to return.
     * @return string Returns the block content or the default if block is not found.
     */
    public static function getBlock(string $name, string $default = '')
    {
        return self::$_blocks[$name] ?? $default;
    }

    /**
     * Pushes content to a layout stack.
     * @param string $name Stack name.
     */
    public static function pushStack(string $name)
    {
        if (self::$_stack) throw new Exception('pushStack(): Stack is already started');
        self::$_stack = $name;
        self::$_prependStack = false;
        ob_start();
    }

    /**
     * Prepends content to the start of a layout stack.
     * @param string $name Stack name.
     */
    public static function prependStack(string $name)
    {
        if (self::$_stack) throw new Exception('pushStack(): Stack is already started');
        self::$_stack = $name;
        self::$_prependStack = true;
        ob_start();
    }

    /**
     * Finishes a layout stack.
     */
    public static function endStack()
    {
        if (!self::$_stack) throw new Exception('endStack(): No stack was started');
        if (empty(self::$_stacks[self::$_stack])) self::$_stacks[self::$_stack] = [];
        $content = ob_get_clean();
        if (self::$_prependStack) {
            array_unshift(self::$_stacks[self::$_stack], $content);
        } else {
            self::$_stacks[self::$_stack][] = $content;
        }
        self::$_stack = null;
    }

    /**
     * Gets a stack content.
     * @param string $name Stack name.
     * @param string $default (Optional) Default content to return.
     * @return string Returns the stack content or the default if block is not found.
     */
    public static function getStack(string $name, string $default = '')
    {
        if (empty(self::$_stacks[$name])) return $default;
        return implode(PHP_EOL, self::$_stacks[$name]);
    }
}
