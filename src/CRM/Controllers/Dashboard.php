<?php

namespace GWM\CRM\Controllers {
    use \GWM\Core\Session;
    use \GWM\Core\Response;
    use \GWM\Core\Controllers\Dashboard as Root;

    use \GWM\Core\Template\Engine as TemplateEngine;

    class Dashboard
    {
        public function getEmployees(Response $res)
        {
            try {
                Session::Get()->Logged() || $res->Astray();

                $res->setContent(TemplateEngine::Get()->Parse(
                    'themes/admin/templates/crm/employees.latte',
                    ...[
                            Root::Defaults(),
                            [
                                
                            ]
                    ]
                ), Response::HTTP_OK)->send();
            } catch (\Exception $e) {
                $res->setContent($e->getMessage(), Response::HTTP_ERROR)->send();
            }
        }
    }
}
