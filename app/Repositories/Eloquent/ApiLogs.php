<?php

namespace App\Repositories\Eloquent;

use App\Models\ApiLogs as ApiLogsModel;
use App\Repositories\Contracts\ApiLogs as ApiLogsContract;

class ApiLogs extends Base implements ApiLogsContract
{
    public function __construct(ApiLogsModel $model)
    {
        parent::__construct($model, ['logs']);
    }
}