<?php
/**
 *
 * @author Nico
 *
 */
class AclRouter {

    /**
     * Get the ACO path of a link. This can be useful for example to check then
     * if a user is allowed to access an url.
     *
     * @param mixed $url Cake-relative URL, like "/products/edit/92" or
     * "/presidents/elect/4" or an array specifying any of the following:
     * 'controller', 'action', and/or 'plugin', in addition to named arguments
     * (keyed array elements), and standard URL arguments (indexed array
     * elements)
     */
    static function acoPath($url) {
        $routedUrl = Router::url($url);
        $routedUrl = str_replace(Router::url('/'), '/', $routedUrl);
        $parsedUrl = Router::parse($routedUrl);

        $acoPath = 'controllers/';

        if (! empty($parsedUrl['plugin'])) {
            $acoPath .= Inflector::camelize($parsedUrl['plugin']) . '/';
        }
        if($parsedUrl['controller'] != 'App') {
        $acoPath .= Inflector::camelize($parsedUrl['controller']) . '/';
        $acoPath .= $parsedUrl['action'];
        }

        return $acoPath;
    }
}