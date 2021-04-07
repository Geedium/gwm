<?php

namespace GWM\Core\Controllers {

    use GWM\Core\ {
        Response,
        Session,
        Schema
    };

    use GWM\Core\Models\User as Model;

    class User
    {
        public function Profile(Response $response, string $username)
        {
            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;
            } catch (Basic $e) {
                die($e->getMessage());
            }

            $stmt = $schema->prepare("SELECT *
            FROM ${_ENV['DB_PREFIX']}_users u
            WHERE username LIKE CONCAT('%', :user, '%') ");
            $stmt->execute([
                ':user' => $username
            ]);

            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
            $username = $data['username'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $avatar = $data['avatar'];
            $id = $data['id'];

            $params = array_merge(\GWM\Core\Controllers\Home::ContextChain(), [
                'inspUsername' => $username,
                'inspFirstname' => $firstname,
                'inspLastname' => $lastname,
                'inspAvatar' => '/images/avatars/'.strtolower($username).'.png'
            ]);

            $html = \GWM\Core\Template\Engine::Get()
            ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/core/user.html.latte", $params);

            $response->setContent($html)->send(200);
            exit;
        }
    }
}