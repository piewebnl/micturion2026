<?php

namespace App\Http\Controllers\LastFmApi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use LastFmApi\Api\AuthApi;

// Make a connection/session to the lastfm API via auth token
class LastFmApiAuthorizeCallbackController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        if ($request->token) {

            Setting::addSetting('last_fm_token', $request->token);

            // Remove cache in vendor lastfm api: $this->cache->set($vars, $this->response);
            try {
                $auth = new AuthApi('getsession', [
                    'apiKey' => config('lastfm.last_fm_api_key'),
                    'apiSecret' => config('lastfm.last_fm_shared_secret'),
                    'token' => Setting::getSetting('last_fm_token'),
                ]);

                Setting::addSetting('last_fm_session_key', $auth->sessionKey);
                session()->flash('success', 'Last FM authorized!');
            } catch (Exception $e) {
                session()->flash('error', 'Token invalid/expired: Change in lastFm composer package: BaseApi.php -> apiGetCall (See notes)');
            }
        } else {
            session()->flash('error', 'No valid Last FM connection');
        }

        return view('last-fm.last-fm-authorize-callback');
    }
}
