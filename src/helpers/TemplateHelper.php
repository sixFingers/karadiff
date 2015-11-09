<?php

namespace Karadiff\Helpers;

class TemplateHelper
{
    /**
     * Returns or outputs the given template populated with given variables.
     *
     * @param  [String]
     * @param  [Array]
     * @param  boolean
     * @return [mixed]
     */
    public static function render($path, $vars)
    {
        $path = __DIR__ . '/../templates/' . $path;
        extract($vars);

        ob_start();
        require_once($path);
        return ob_get_clean();
    }
}
