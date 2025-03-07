<?php
// Stripped down (like, REALLY) version from Laracasts.

class Router {
    private $routes = [];

    public function add($method, $uri, $controller) {
        $new_route = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'restricted' => false
        ];

        array_push($this->routes, $new_route);

        return $this;
    }

    public function route($target_uri, $target_method) {

        foreach ($this->routes as $current_route) {
     
            if ($this->route_matches($target_uri, $target_method, $current_route)) {
                
                if ($current_route['restricted']) {
                    $this->authorize();
                }
                return require "../controllers/{$current_route['controller']}";
            }
        }


        http_response_code(404);
        require path_to('views/404.php'); // not a redirect
        die();
    }

    private function authorize() {
        if (!isset($_SESSION['authorized'])) {
            redirect('/admin');
        }
    }


    public function restricted() {
        $last_routes_index = array_key_last($this->routes);
        $this->routes[$last_routes_index]['restricted'] = true;
    }

    private function route_matches($target_uri, $target_method, $route) {
        return $route['uri'] === $target_uri && strcasecmp($route['method'], $target_method) === 0;
    }
}
