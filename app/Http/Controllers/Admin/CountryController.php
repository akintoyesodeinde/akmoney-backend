<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\ConversionRate;
use App\Models\CurrencyConversionRate;
use App\Models\Country;
use App\Models\CountryDeliveryMethod;
use App\Models\DeliveryCharge;
use App\Models\GatewayCurrency;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $pageTitle   = 'All Countries';
        $countryList = getCountryList();
        $countries   = Country::searchable(['name', 'currency'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.country.index', compact('pageTitle', 'countries', 'countryList'));
    }

    public function store(Request $request)
    {
        $this->validation($request);
        $country  = new Country();
        $this->saveCountry($request, $country);
        $notify[] = ['success', 'Country added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, $id);
        $country  = Country::findOrFail($id);
        $this->saveCountry($request, $country);
        $notify[] = ['success', 'Country updated successfully'];
        return back()->withNotify($notify);
    }

    public function saveCountry(Request $request, $country)
    {
        $countryList             = getCountryList();
        $countryCode             = $request->country_code;
        $country->name           = @$countryList->$countryCode->country;
        $country->country_code   = $countryCode;
        $country->dial_code      = @$countryList->$countryCode->dial_code;
        $country->currency       = $request->currency;
        $country->rate           = $request->rate;
        $country->is_sending     = $request->is_sending ? Status::YES : Status::NO;
        $country->is_receiving   = $request->is_receiving ? Status::YES : Status::NO;
        $country->has_agent      = $request->has_agent ? Status::YES : Status::NO;

        if ($request->hasFile('image')) {
            try {
                $country->image = fileUploader($request->image, getFilePath('country'), getFileSize('country'), @$country->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $country->save();

        GatewayCurrency::syncRate($country->currency, (float) $country->rate);
        $this->syncConversionRatesForCountry($country);
    }

    private function syncConversionRatesForCountry(Country $country): void
    {
        $newRate = (float) $country->rate;
        if ($newRate <= 0) return;

        $baseCountry = Country::active()->where('currency', gs('cur_text'))->first();
        if (!$baseCountry) return;

        $others = Country::active()->where('id', '!=', $country->id)->get();

        if ($baseCountry->id !== $country->id) {
            // base → this country
            CurrencyConversionRate::updateOrCreate(
                ['from_country' => $baseCountry->id, 'to_country' => $country->id],
                ['rate' => $newRate]
            );
            // this country → base
            CurrencyConversionRate::updateOrCreate(
                ['from_country' => $country->id, 'to_country' => $baseCountry->id],
                ['rate' => round(1 / $newRate, 8)]
            );
        }

        foreach ($others as $other) {
            if ($other->id === $baseCountry->id) continue;
            $otherRate = (float) $other->rate;
            if ($otherRate <= 0) continue;

            // this → other
            CurrencyConversionRate::updateOrCreate(
                ['from_country' => $country->id, 'to_country' => $other->id],
                ['rate' => round($otherRate / $newRate, 8)]
            );
            // other → this
            CurrencyConversionRate::updateOrCreate(
                ['from_country' => $other->id, 'to_country' => $country->id],
                ['rate' => round($newRate / $otherRate, 8)]
            );
        }
    }

    protected function validation($request, $id = 0)
    {
        $validationRules = [
            'country_code' => 'required|unique:countries,country_code,' . $id,
            'currency'     => 'required',
            'rate'         => 'required|numeric|gt:0',
            'is_sending'   => 'nullable|in:on',
            'is_receiving' => 'nullable|in:on',
            'has_agent'    => 'nullable|in:on'
        ];

        if ($id) {
            $imageValidation = 'nullable';
        } else {
            $imageValidation = 'required';
        }
        $validationRules['image'] = [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
        $request->validate($validationRules);
    }

    public function updateStatus($id)
    {
        return Country::changeStatus($id);
    }

    //currency conversion
    public function currencyConversionRate($id)
    {
        $country   = Country::with('conversionRates')->findOrFail($id);
        $pageTitle = "Conversion Rates from {$country->currency} ({$country->name})";

        // Only show active countries — inactive ones are irrelevant for send/receive
        $countries = Country::active()->where('id', '!=', $id)->orderBy('name')->get();

        // Existing manual rates stored for this country
        $rates = $country->conversionRates->keyBy('to_country');

        // For entries with no manual rate, derive from base-currency rates:
        // if CAD→this and CAD→target both exist, then this→target = (CAD→target) / (CAD→this)
        $baseCountry = Country::active()->where('currency', gs('cur_text'))->first();
        if ($baseCountry && $baseCountry->id !== $country->id) {
            $baseRates = $baseCountry->conversionRates->keyBy('to_country');
            $thisBaseRate = $baseRates->get($country->id)?->rate ?? $country->rate;

            foreach ($countries as $target) {
                if ($rates->has($target->id)) continue; // already has manual rate

                $targetBaseRate = $baseRates->get($target->id)?->rate ?? $target->rate;
                if ($thisBaseRate > 0 && $targetBaseRate > 0) {
                    $derived = round($targetBaseRate / $thisBaseRate, 8);
                    // Inject a synthetic (non-persisted) rate object for the view
                    $rates->put($target->id, (object) [
                        'to_country' => $target->id,
                        'rate'       => $derived,
                        'derived'    => true,
                    ]);
                }
            }
        }

        return view('admin.country.conversion_rates', compact('countries', 'pageTitle', 'country', 'rates'));
    }

    public function saveCurrencyConversionRates(Request $request, $id)
    {
        $countryIds = Country::active()->pluck('id')->toArray();
        $request->validate([
            'data' => 'required|array|min:1',
            'data.*.from_country' => 'in:' . implode(',', $countryIds),
            'data.*.to_country' => 'in:' . implode(',', $countryIds),
            'data.*.rate' => 'nullable|numeric|min:0'
        ], [
            'data.*.from_country.in' => 'Invalid from country',
            'data.*.to_country.in' => 'Invalid To country',
            'data.*.rate.numeric' => 'Rate should be numeric value',
            'data.*.rate.min' => 'Rate should be greater than or equal to 0',
        ]);

        $rates = $request->data;

        foreach ($rates as $key => $data) {
            if ($data['rate'] * 1 == 0) {
                unset($rates[$key]);
            }
        }

        $rates = array_values($rates);

        CurrencyConversionRate::where('from_country', $id)->delete();
        CurrencyConversionRate::insert($rates);

        // Sync: for each saved rate, update the destination country's rate field
        // and propagate to gateway currencies so everything stays consistent.
        $fromCountry  = Country::find($id);
        $fromRate     = (float) ($fromCountry->rate ?: 1);
        $countryMap   = Country::active()->get()->keyBy('id');

        foreach ($rates as $row) {
            $toId   = $row['to_country'];
            $rate   = (float) $row['rate'];
            if ($rate <= 0) continue;

            $toCountry = $countryMap->get($toId);
            if (!$toCountry) continue;

            // Convert to base-currency rate:
            // if FROM is base (rate=1), $rate is already in base units
            // if FROM is not base, multiply: base→TO = (base→FROM) * (FROM→TO)
            $baseRate = $fromRate === 1.0
                ? $rate
                : round($fromRate * $rate, 8);

            if ($toCountry->rate != $baseRate) {
                $toCountry->rate = $baseRate;
                $toCountry->save();
                GatewayCurrency::syncRate($toCountry->currency, $baseRate);
            }
        }

        // Also store inverse rates (FROM = each to_country, TO = this country)
        // so other pages show populated rates instead of blank.
        $fromCurrencyRate = $fromRate;
        $inverseRows = [];
        foreach ($rates as $row) {
            $rate = (float) $row['rate'];
            if ($rate <= 0) continue;
            $inverseRows[] = [
                'from_country' => $row['to_country'],
                'to_country'   => $id,
                'rate'         => round(1 / $rate, 8),
            ];
        }
        if ($inverseRows) {
            foreach ($inverseRows as $inv) {
                CurrencyConversionRate::where('from_country', $inv['from_country'])
                    ->where('to_country', $inv['to_country'])
                    ->delete();
            }
            CurrencyConversionRate::insert($inverseRows);
        }

        $notify[] = ['success', 'Currency conversion rates updated successfully'];
        return back()->withNotify($notify);
    }

    public function setCharges($id)
    {
        $country = Country::with('countryDeliveryMethods.deliveryMethod', 'countryDeliveryMethods.charge')->findOrFail($id);
        $pageTitle = 'Set Charges for the Delivery Methods in ' . $country->name;
        return view('admin.country.set_charges', compact('country', 'pageTitle'));
    }

    public function saveCharges(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $request->validate([
            'delivery_method_id' => 'required|exists:delivery_methods,id',
            'fixed_charge'       => 'required|numeric|gte:0',
            'percent_charge'     => 'required|numeric|gte:0'
        ]);

        $countryDeliveryMethod  = CountryDeliveryMethod::where('country_id', $country->id)->where('delivery_method_id', $request->delivery_method_id)->first('id');
        $charge                 = DeliveryCharge::where('country_delivery_method_id', $countryDeliveryMethod->id)->first() ?? new DeliveryCharge();

        $charge->country_delivery_method_id   = $countryDeliveryMethod->id;
        $charge->fixed_charge   = $request->fixed_charge;
        $charge->percent_charge = $request->percent_charge;
        $charge->save();

        $notify[] = ['success', 'Delivery charge updated successfully'];
        return back()->withNotify($notify);
    }
    public function synchronizeViaApi(Request $request, $id)
    {
        $country          = Country::findOrFail($id);
        $conversionConfig = gs('conversion_rate_api');
        if (!@$conversionConfig->status) {
            $notify[] = ['error', 'Conversion rate api is not configured'];
            return back()->withNotify($notify);
        }
        $conversionRate             = new ConversionRate();
        $conversionRate->exceptions = true;

        try {
            // Always sync from the base currency so all country rates are correct.
            // If a non-base country was requested, also sync from that country.
            $baseCountry = Country::active()->where('currency', gs('cur_text'))->first();
            if ($baseCountry) {
                $conversionRate->sync($baseCountry);
            }
            if ($baseCountry?->id !== $country->id) {
                $conversionRate->sync($country);
            }
        } catch (\Throwable $th) {
            $notify[] = ['error', $th->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Rates synchronized successfully'];
        return back()->withNotify($notify);
    }
}
