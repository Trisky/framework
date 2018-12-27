<?php
namespace OffbeatWP\Components;

use Symfony\Component\EventDispatcher\EventDispatcher;

class ComponentRepository {

    public $eventDispatcher;

    public function __construct (EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function register($name, $componentClass)
    {
        $event = new EventRegisterComponent($name, $componentClass);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if ($componentClass::supports('widget')) {
            $this->registerWidget($componentClass);
        }

        if ($componentClass::supports('shortcode')) {
            $this->registerShortcode($componentClass);
        }

        $this->components[$name] = $componentClass;
    }

    public function registerWidget($componentClass) {
        $componentSettings = $componentClass::settings();

        $widget_settings = [
            'id_base'   => $componentSettings['slug'],
            'name'      => $componentSettings['name']
        ];

        $widget = new GenericWidget($widget_settings, $componentClass);

        register_widget($widget);
    }

    public function registerShortcode($componentClass) {
        $app = raowApp();

        $componentSettings = $componentClass::settings();

        add_shortcode('raow-' . $componentSettings['slug'], function ($atts, $content = '') use ($app, $componentClass) {
            $shortcode = $app->container->make(GenericShortcode::class, ['componentClass' => $componentClass]);
            return $shortcode->renderShortcode($atts, $content);
        });
    }

    public function get($name = null)
    {
        if (is_null($name))
        {
            return $this->components;
        }

        if (isset($this->components[$name]))
        {
            return $this->components[$name];
        }

        throw new \Exception("Component does not exists ({$name})");
    }

    public function make($name)
    {
        $componentClass = $this->get($name);
        return raowApp()->container->make($componentClass);
    }

    public function exists($name)
    {
        if (isset($this->components[$name]))
        {
            return true;
        }

        return false;
    }

    public function render($name, $args = []) {
        $component = $this->make($name);

        return container()->call([$component, 'render'], ['settings' => (object) $args]);
    }
}