<?php

namespace GWM\Reactive\Components {

    use GWM\Core\Model;
    use GWM\Core\Response;
    use GWM\Core\Schema;
    use GWM\Core\Session;
    use GWM\Core\Utils\Wrap;

    /**
     * Class Table
     * @package GWM\Reactive\Components
     */
    class Table
    {
        private $_ = '';
        protected array $fields = [];
        protected string $model = '';
        protected string $identifier = '';
        private array $data = [];

        public function Handle(Response $response, Schema $schema): void
        {
            Wrap::isStringNotEmpty(function ($identifier) use ($response) {
                if (filter_input(INPUT_POST, "{$identifier}_submit", FILTER_VALIDATE_BOOL)) {
                    $_SESSION['POST'] = $_POST;
                    $response->Redirect();
                }
            }, $this->identifier);
        }

        /**
         * @param Model $model
         * @since 1.0.0
         */
        public function Hint(Model $model): void
        {
            Wrap::Reflection(function () use ($model) {
                $class = new \ReflectionClass($model);
                $name = $class->getShortName();
                $this->identifier = 'form_' . strtolower($name);
                $properties = $class->getProperties(
                    \ReflectionProperty::IS_PUBLIC |
                    \ReflectionProperty::IS_PROTECTED);
                foreach ($properties as $property) {
                    $this->fields[] = [
                        'name' => $property->getName(),
                        'type' => (string)$property->getType()
                    ];
                }
            });
        }

        function __toString(): string
        {
            return $this->_;
        }
    }
}