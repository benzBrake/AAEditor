<?php
namespace TypechoPlugin\AAEditor;
use Typecho\Widget\Helper\Layout;

class Label extends Layout
{
    public function __construct($html)
    {
        $this->html($html);
        $this->start();
        $this->end();
    }

    public function start()
    {
    }

    public function end()
    {
    }
}
