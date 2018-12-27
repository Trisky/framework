<?php
namespace OffbeatWP\Components;

use OffbeatWP\Services\AbstractService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ComponentsService extends AbstractService
{
    public $bindings = [
        'components' => ComponentRepository::class
    ];

    public function register(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener('raow.ready', [$this, 'registerComponents']);
    }

    public function registerComponents()
    {
        $components = $this->registrableComponents();

        if (!empty($components)) {
            foreach ($components as $component => $class) {
                container('components')->register(lcfirst($component), $class);
            }
        }
    }

    public function registrableComponents()
    {
        $activeComponents = [];
        $componentsDirectory = $this->getComponentsDirectory();

        if (!is_dir($componentsDirectory)) return null;

        if ($handle = opendir($componentsDirectory)) {
            while (false !== ($entry = readdir($handle))) {
                if (!is_dir($componentsDirectory . '/' . $entry) || preg_match('/^(_|\.)/', $entry)) continue;

                $activeComponents[] = $entry;
            }

            closedir($handle);
        }

        $components = [];

        foreach ($activeComponents as $activeComponent) {
            $components[$activeComponent] = "Components\\" . $activeComponent . "\\" . $activeComponent;
        }

        return array_unique($components);
    }

    public function getComponentsDirectory()
    {
        $componentsDirectory = $this->app->componentsPath();

        return $componentsDirectory;
    }
}