<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\CommonService;

class AchievementTimeTypeController extends Controller
{
    private CommonService $common_service;

    public function __construct(CommonService $common_service)
    {
        $this->common_service = $common_service;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return $this->common_service->getAchievementTimeType();
    }
}
