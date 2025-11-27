<?php

namespace Modules\MainApp\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Services\SettingContextService;
use App\Interfaces\MainSettingInterface;
use Modules\MainApp\Http\Repositories\SettingRepository;
use Modules\MainApp\Http\Requests\Settings\GeneralSettingStoreRequest;

class SettingController extends Controller
{
    private $settingRepo;
    private $mainSettingRepo;
    private $contextService;

    function __construct(
        SettingRepository $settingRepo,
        MainSettingInterface $mainSettingRepo,
        SettingContextService $contextService
    )
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->settingRepo = $settingRepo;
        $this->mainSettingRepo = $mainSettingRepo;
        $this->contextService = $contextService;
    }

    public function generalSettings()
    {
        $data['title']      = ___('common.general_settings');
        $data['languages']  = $this->settingRepo->getLanguage();
        $data['currencies'] = $this->settingRepo->getCurrencies();

        // Use main settings for system admin, school settings otherwise
        if ($this->contextService->shouldUseMainSettings()) {
            $data['data'] = $this->mainSettingRepo->all();
            $data['is_main_settings'] = true;
        } else {
            $data['data'] = $this->settingRepo->getAll();
            $data['is_main_settings'] = false;
        }

        return view('mainapp::settings.general-settings', compact('data'));
    }

    public function updateGeneralSetting(GeneralSettingStoreRequest $request)
    {
        // Use main settings repository for system admin, school settings otherwise
        if ($this->contextService->shouldUseMainSettings()) {
            $result = $this->mainSettingRepo->updateGeneralSettings($request);
        } else {
            $result = $this->settingRepo->updateGeneralSetting($request);
        }

        if ($result) {
            return redirect()->back()->with('success', ___('alert.general_settings_updated_successfully'));
        }
        return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
    }
}
