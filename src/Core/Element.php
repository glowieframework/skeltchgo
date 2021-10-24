<?php
    namespace Glowie\SkeltchGo\Core;

    use Glowie\SkeltchGo\Core\ElementTrait;

    /**
     * Generic safe object instance for SkeltchGo.
     * @category Object
     * @package glowieframework/skeltchgo
     * @author Glowie
     * @copyright Copyright (c) 2021
     * @license MIT
     * @link https://glowie.tk
     * @version 1.0
     */
    class Element{
        use ElementTrait;

        /**
         * Creates a new Element.
         * @param array $data (Optional) An associative array with the initial data to parse.
         */
        public function __construct(array $data = []){
            $this->__constructTrait($data);
        }

    }

?>