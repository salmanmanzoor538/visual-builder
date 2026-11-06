<?php

namespace App\Http\Controllers;

use App\Models\BuilderForm;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function store(Request $request, string $website, BuilderForm $form): JsonResponse
    {
        $site = Website::query()->where('slug', $website)->where('status', 'published')->firstOrFail();
        abort_unless($form->website_id === $site->id, 404);
        abort_if($request->filled('_company'), 422, 'Unable to accept this submission.');

        $rules = [];
        foreach ($form->fields as $field) {
            $rules[$field->name] = array_filter([
                $field->required ? 'required' : 'nullable',
                $field->type === 'email' ? 'email' : 'string',
                'max:5000',
            ]);
        }
        if ($form->requires_consent) {
            $rules['_consent'] = ['accepted'];
        }
        $payload = $request->validate($rules);
        unset($payload['_consent']);
        $form->submissions()->create([
            'payload' => $payload,
            'ip_hash' => hash_hmac('sha256', (string) $request->ip(), config('app.key')),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'consented_at' => $form->requires_consent ? now() : null,
        ]);

        return response()->json(['message' => $form->success_message], 201);
    }
}
