<?php

namespace App\Http\Controllers;

use App\Actions\Billing\BuildPlanSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BillingController extends Controller
{
    public function index(Request $request, BuildPlanSummary $planSummary): Response
    {
        return Inertia::render('billing', ['plan' => $planSummary->handle($request->user())]);
    }

    public function checkout(Request $request, BuildPlanSummary $planSummary): SymfonyResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $planSummary->handle($user)['billing_configured']) {
            return back()->with('error', 'Billing is not available right now.');
        }

        if ($user->subscribed()) {
            return back()->with('error', 'You are already on the Pro plan.');
        }

        $checkout = $user->newSubscription('default', config('cashier.pro_price'))->checkout([
            'success_url' => route('billing.index').'?checkout=success',
            'cancel_url' => route('billing.index'),
        ]);

        return Inertia::location($checkout->url);
    }

    public function portal(Request $request, BuildPlanSummary $planSummary): SymfonyResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $planSummary->handle($user)['billing_configured'] || ! $user->hasStripeId()) {
            return back()->with('error', 'Billing portal is not available.');
        }

        return Inertia::location($user->billingPortalUrl(route('billing.index')));
    }
}
