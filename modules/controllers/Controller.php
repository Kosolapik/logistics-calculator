<?php
    namespace Controllers {
        class Controller {
            static function render(string $template, array $context) {
                global $base_path;
                extract($context);
                require $base_path . '\modules\templates\\' . $template . '.php';
            }
        }
    }
?>