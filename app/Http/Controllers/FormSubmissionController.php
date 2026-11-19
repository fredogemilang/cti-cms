<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    /**
     * Display the form.
     */
    public function show($slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        return view('forms.show', compact('form'));
    }

    /**
     * Handle form submission.
     */
    public function submit(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        // Process the submission (validation + entry creation + notifications)
        $result = $form->processSubmission($request->all(), $request);

        if (! $result['success']) {
            return back()
                ->withErrors($result['errors'])
                ->withInput();
        }

        // Notifications are dispatched inside processSubmission() via FormNotificationService.

        // Handle confirmation based on type
        $confirmations = $form->confirmations ?? [];
        $confirmationType = $confirmations['type'] ?? 'message';
        $successMessage = $form->getConfirmationMessage();

        switch ($confirmationType) {
            case 'redirect':
                $redirectUrl = $form->getConfirmationRedirectUrl();

                // Only allow internal redirects to prevent open redirect attacks
                $parsed = parse_url($redirectUrl);
                $isInternal = empty($parsed['host']) || $parsed['host'] === request()->getHost();
                if (! $isInternal) {
                    $redirectUrl = url('/');
                }

                return redirect($redirectUrl)->with('success', $successMessage);

            case 'success_page':
                // Redirect to form's dedicated success page
                return redirect()->route('forms.success', $slug)
                    ->with('form_success_message', $successMessage);

            case 'message':
            default:
                return back()->with('success', $successMessage);
        }
    }

    /**
     * Display the form success page.
     */
    public function success($slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        $title = 'Thank You!';
        $message = $form->getConfirmationMessage();

        return view('forms.success', compact('form', 'message', 'title'));
    }

    /**
     * AJAX submission handler.
     */
    public function submitAjax(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        // Process the submission (validation + entry creation + notifications)
        $result = $form->processSubmission($request->all(), $request);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'errors' => $result['errors'],
            ], 422);
        }

        $confirmations = $form->confirmations ?? [];
        $successMessage = $form->getConfirmationMessage();

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'entry_id' => $result['entry']->id,
            'redirect_url' => ($confirmations['type'] ?? null) === 'redirect'
                ? $form->getConfirmationRedirectUrl()
                : null,
        ]);
    }
}
