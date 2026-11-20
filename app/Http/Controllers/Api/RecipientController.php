<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecipientController extends Controller
{
    public function index()
    {
        $recipients = Recipient::where('user_id', auth()->id())
            ->with('country:id,name', 'deliveryMethod:id,name', 'service:id,name')
            ->latest()
            ->get();

        return response()->json([
            'remark' => 'recipients',
            'status' => 'success',
            'data'   => ['recipients' => $recipients],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'mobile'  => 'nullable|string|max:20',
            'email'   => [
                'nullable', 'email',
                Rule::unique('recipients')->where(fn($q) => $q->where('user_id', auth()->id())),
            ],
            'address'              => 'nullable|string|max:500',
            'country_id' => 'nullable|integer',
            'delivery_method_id'   => 'nullable|integer',
            'service_id'           => 'nullable|integer',
            'service_form_data'    => 'nullable|array',
        ]);

        $recipient = Recipient::create([
            'user_id'              => auth()->id(),
            'name'                 => $request->name,
            'mobile'               => $request->mobile,
            'email'                => $request->email,
            'address'              => $request->address,
            'country_id' => $request->country_id ?: null,
            'delivery_method_id'   => $request->delivery_method_id   ?: null,
            'service_id'           => $request->service_id           ?: null,
            'service_form_data'    => $request->service_form_data    ?: null,
        ]);

        return response()->json([
            'remark'  => 'recipient_created',
            'status'  => 'success',
            'message' => ['success' => ['Recipient created successfully']],
            'data'    => ['recipient' => $recipient->load('country:id,name', 'deliveryMethod:id,name', 'service:id,name')],
        ], 201);
    }

    public function show($id)
    {
        $recipient = Recipient::where('user_id', auth()->id())
            ->with('country:id,name', 'deliveryMethod:id,name', 'service:id,name')
            ->find($id);

        if (!$recipient) {
            return response()->json([
                'remark'  => 'recipient_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Recipient not found'],
            ], 404);
        }

        return response()->json([
            'remark' => 'recipient',
            'status' => 'success',
            'data'   => ['recipient' => $recipient],
        ]);
    }

    public function update(Request $request, $id)
    {
        $recipient = Recipient::where('user_id', auth()->id())->find($id);

        if (!$recipient) {
            return response()->json([
                'remark'  => 'recipient_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Recipient not found'],
            ], 404);
        }

        $request->validate([
            'name'    => 'required|string|max:255',
            'mobile'  => 'nullable|string|max:20',
            'email'   => [
                'nullable', 'email',
                Rule::unique('recipients')
                    ->where(fn($q) => $q->where('user_id', auth()->id()))
                    ->ignore($recipient->id),
            ],
            'address'              => 'nullable|string|max:500',
            'country_id' => 'nullable|integer',
            'delivery_method_id'   => 'nullable|integer',
            'service_id'           => 'nullable|integer',
            'service_form_data'    => 'nullable|array',
        ]);

        $recipient->update([
            'name'                 => $request->name,
            'mobile'               => $request->mobile,
            'email'                => $request->email,
            'address'              => $request->address,
            'country_id' => $request->country_id ?: null,
            'delivery_method_id'   => $request->delivery_method_id   ?: null,
            'service_id'           => $request->service_id           ?: null,
            'service_form_data'    => $request->has('service_form_data')
                ? ($request->service_form_data ?: null)
                : $recipient->service_form_data,
        ]);

        return response()->json([
            'remark'  => 'recipient_updated',
            'status'  => 'success',
            'message' => ['success' => ['Recipient updated successfully']],
            'data'    => ['recipient' => $recipient->fresh()->load('country:id,name', 'deliveryMethod:id,name', 'service:id,name')],
        ]);
    }

    public function destroy($id)
    {
        $recipient = Recipient::where('user_id', auth()->id())->find($id);

        if (!$recipient) {
            return response()->json([
                'remark'  => 'recipient_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Recipient not found'],
            ], 404);
        }

        $recipient->delete();

        return response()->json([
            'remark'  => 'recipient_deleted',
            'status'  => 'success',
            'message' => ['success' => ['Recipient deleted successfully']],
        ]);
    }
}
