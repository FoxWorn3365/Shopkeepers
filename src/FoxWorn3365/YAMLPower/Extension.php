<?php

namespace FoxWorn3365\YAMLPower;

class Extension {
    protected object $extensions;
    public string $dir = 'yaml_ext';
    protected Error $error;
    public array $list = [];
    public object $association;

    public function __construct(Error $error, array $exists = []) {
        $this->extensions = new \stdClass; 
        $this->association = new \stdClass;
        $this->error = $error;
        $this->list = $exists;
    }

    public function has(string $name) : bool {
        if (@$this->extensions->{$name} !== null) {
            return true;
        }
        return false;
    }

    public function get(string $name) : object|null {
        if (@$this->extensions->{$name} === null) {
            $this->error->throw('methodInClassNotFoundException', true);
            return null;
        } else {
            return $this->extensions->{$name};
        }
    }

    public function getMethod(string $name) : object {
        return $this->association->{$name};
    }

    public function load(string $name) : void {
        if (!file_exists("{$this->dir}/{$name}.php")) {
            $this->error->throw('extensionFileNotFoundException', true);
        }

        // Now load the file by including and loading the extension class
        require "{$this->dir}/{$name}.php";

        $class = new $name();

        if (!@$class->extension) {
            $this->error->throw('noExtensionFileInExtensionFolderException', true, [$name]);
        } elseif (gettype(@$class->functions) === 'array') {
            $this->extensions->{$name} = $class;

            foreach ($class->functions as $function) {
                if (!method_exists($class, $function . '_executor')) {
                    $this->error->throw('methodInClassNotFoundException', true, [$function . '_executor']);
                } else {
                    $this->list[] = $function;
                    $this->association->{$function} = $class;
                }
            }
        } else {
            $this->error->throw('noFunctionListAvailableOnExtensionException', true, [$name]);
        }
    }
}