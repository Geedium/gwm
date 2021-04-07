<?php

namespace GWM\Core\Facades {

    use GWM\Core\ {
        Schema,
        Session
    };

    use GWM\Core\Models\User as Model;

    /**
     * User
     */
    class User
    {
        private Schema $schema;

        private Session $session;

        /** @magic */
        public function __construct(Schema $schema = null, Session $session = null)
        {
            if (!$schema && !$session) {
                $this->schema = new Schema($_ENV['DB_NAME']);
                $this->session = Session::Get();
            } elseif (!$schema) {
                $this->schema = new Schema($_ENV['DB_NAME']);
                $this->session = $session;
            } elseif (!$session) {
                $this->session = Session::Get();
                $this->schema = $schema;
            }
        }

        public function construct(): ?Model
        {
            $stmt = $this->schema->prepare("SELECT id, username, role
            FROM {$_ENV['DB_PREFIX']}_users u
            WHERE u.token = :token");
            
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Model::class);

            if (!isset($_COOKIE['JWT_TOKEN']) & true) {
                return new Model;
            }

            $stmt->execute([
                ':token' => $_COOKIE['JWT_TOKEN']
            ]);

            $model = $stmt->fetch();

            if(!$model)
            {
                return new Model;
            }

            return $model;
        }
    }
}