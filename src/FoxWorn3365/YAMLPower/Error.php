<?php

namespace FoxWorn3365\YAMLPower;

class Error {
    public object $manager;
    public array $exceptions = [
        'notArrayException',
        'noVarException',
        'notAllArgsException',
        'noRequredArgForSpecificTaskException',
        'incredibleArgException',
        'noExceptionException',
        'undefinedRequestException',
        'invalidClassException',
        'tooMuchArgsForClassFactoryOrExecutionException',
        'methodInClassNotFoundException',
        'extensionFileNotFoundException',
        'noExtensionFileInExtensionFolderException',
        'noFunctionListAvailableOnExtensionException',
        'wrongCollectCallException',
        'wrongTypeException'
    ];

    public function __construct() {
        $this->manager = new \stdClass;
    }

    public function throw(string $ex, bool $kill = false, array $data = []) : void {
        if (in_array($ex, $this->exceptions)) {
            if (@$this->manager->{$ex} !== null) {
                ($this->manager->{$ex})($data);
            }
        } else {
            if (@$this->manager->noExceptionException !== null) {
                ($this->manager->noExceptionException)($data);
            }
        }

        ($this->manager->__global)($ex, $data);

        if ($kill) {
            die();
        }
    }

    public function handle(string $ex, callable $callback) : void {
        $this->manager->{$ex} = $callback;
    }

    public function globalHandler(callable $callback) : void {
        $this->manager->__global = $callback;
    }
}