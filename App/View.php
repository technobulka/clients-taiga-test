<?php

namespace App;

class View
{
    protected $data = [];

    public function set($args = [])
    {
        $this->data = array_merge($this->data, $args);
    }

    public function display($template)
    {
        foreach ($this->data as $key => $value) {
            $$key = $value;
        }

        include __DIR__ . "/templates/elements/header.php";
        include __DIR__ . "/templates/$template.php";
        include __DIR__ . "/templates/elements/footer.php";
    }
}