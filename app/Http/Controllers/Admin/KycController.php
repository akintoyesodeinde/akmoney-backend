<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\KycProvider;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;

class KycController extends Controller
{
    // public function setting()
    // {
    //     $type = last(request()->segments());
    //     if ($type != 'agent' && $type != 'personal-user' && $type != 'business-user') {
    //         abort(404);
    //     }
    //     $pageTitle = ucwords(keyToTitle($type)) . ' ' . 'KYC Setting';
    //     $form = Form::where('act', $type . '.kyc')->first();
    //     return view('admin.kyc.setting', compact('pageTitle', 'form', 'type'));
    // }
    
    public function setting()
    {
        $pageTitle = 'KYC Providers Settings';
        $type = last(request()->segments());
        if ($type != 'agent' && $type != 'personal-user' && $type != 'business-user') {
            abort(404);
        }
        $form = Form::where('act', $type . '.kyc')->first();
        $providers = KycProvider::orderBy('id')->get();
    
        return view('admin.kyc.setting', compact('pageTitle', 'providers', 'form'));
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            // 'slug' => 'required|string|unique:kyc_providers,slug,' . $request->id,
            // 'name' => 'required|string',
            'is_enabled' => 'nullable',
            'api_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            // 'help_text' => 'nullable|string',
            // 'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
    
        $provider = $request->id
            ? KycProvider::findOrFail($request->id)
            : new KycProvider();

        if ($request->has('is_enabled')) {
            KycProvider::where('id', '!=', $provider->id)->update(['is_enabled' => false]);
        }
        
        // $provider->name = $request->name;
        // $provider->slug = $request->slug;
        // if ($request->hasFile('logo')) {
        //     $file = $request->file('logo');
        //     $filename = fileUploader(
        //         $file,
        //         'assets/images/providers',   // direct path
        //         '350x350',                      // optional
        //         $provider->logo                    // old file to delete
        //     );
        
        //     $provider->logo = $filename;
        // }

        $provider->api_key = $request->api_key;
        $provider->secret_key = $request->secret_key;
        // $provider->help_text = $request->help_text;
        $provider->is_enabled = $request->has('is_enabled');

        $provider->save();
    
        return back()->with('success', 'KYC provider saved successfully');
    }

    public function settingUpdate(Request $request)
    {
        $type = last(request()->segments());
        if ($type != 'agent' && $type != 'personal-user' && $type != 'business-user') {
            abort(404);
        }
        $formProcessor = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', $type . '.kyc')->first();
        $formProcessor->generate($type . '.kyc', $exist, 'act');

        $notify[] = ['success', 'KYC data updated successfully'];
        return back()->withNotify($notify);
    }

    public function module()
    {
        $pageTitle = 'KYC Modules';
        $modules = gs()->kyc_modules;
        return view('admin.kyc.module', compact('pageTitle', 'modules'));
    }

    public function moduleUpdate(Request $request)
    {

        $request->validate([
            'modules' => 'array|max:2',
            'modules.*' => 'nullable|array',
            'modules.*.*' => 'in:on',
        ]);

        $general = gs();
        $general->kyc_modules = $request->modules;
        $general->save();
        $notify[] = ['success', 'KYC Modules updated successfully'];
        return back()->withNotify($notify);
    }
}
