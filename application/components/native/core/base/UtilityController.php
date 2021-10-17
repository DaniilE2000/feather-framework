<?php

namespace application\components\native\core\base;

class UtilityController extends BaseController
{
    public function actionNotFound()
    {
        return 'Error 404. Page ' . $this->route->url . ' not found.';
    }
}

?>