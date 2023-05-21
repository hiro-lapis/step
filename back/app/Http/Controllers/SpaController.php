<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;
use App\Services\StepService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpaController extends Controller
{
    public function __construct(
        private StepService $step_service
    )
    {}

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // $userAgent = $request->header('User-Agent');
        // $is_twitter_crowler = strpos($userAgent, 'Twitterbot') !== false;
        $enable_ogp = false;
        $image_url = '';
        $summary = '';
        $url = '';
        $title = '';
        // URLがステップ詳細かどうか判定した上でOGP情報設定
        $url = $request->url();
        $id = Str::after($url, config('app.url').'/steps/');
        $enable_ogp = Validator::make(['value' => $id], ['value' => 'integer|exists:steps,id'])->passes();
        if ($enable_ogp) {
            $step = Step::find($id);
            $title = $step->name;
            $image_url = $step->ogp_image_url;
            $summary = $step->ogp_summary;
        }
        return view('spa')->with(compact('enable_ogp', 'image_url', 'summary', 'title', 'url'));
    }
}
