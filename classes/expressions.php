<?php

abstract class expression {

    abstract protected function evaluate();
}

class leaf extends expression {

    private $posting;

    public function __construct($term) {
        $this->term = $term;
    }

    public function evaluate() {
        return $this->term;
    }

}

class notEx extends expression {

    protected $op;

    public function __construct(expression $op) {
        $this->op = $op;
    }

    public function evaluate() {
        
    }

}

class andEx extends expression {

    protected $left;
    protected $right;

    public function __construct(expression $left, expression $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function evaluate() {
        return array_intersect_key($this->left->evaluate(), $this->right->evaluate());
    }

}

class orEx extends expression {

    protected $left;
    protected $right;

    function __construct(expression $left, expression $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function evaluate() {
        return array_merge($this->left->evaluate(), $this->right->evaluate());
    }

}
