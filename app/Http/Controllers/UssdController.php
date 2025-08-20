<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\UssdHandler;

class UssdController extends Controller
{
    /**
     * Handle USSD callback from Africa's Talking
     */
    public function callback(Request $request)
    {
        // Handle GET requests (for testing)
        if ($request->isMethod('GET')) {
            return response("CON USSD Callback URL Working!\nThis endpoint accepts POST requests from Africa's Talking.\nTest your USSD code by dialing it on your phone.")
                ->header('Content-Type', 'text/plain');
        }

        // Log the incoming request for debugging
        Log::info('USSD Callback received', $request->all());

        $sessionId = $request->input('sessionId');
        $serviceCode = $request->input('serviceCode');
        $phoneNumber = $request->input('phoneNumber');
        $text = $request->input('text');

        // Create USSD handler instance
        $ussdHandler = new UssdHandler($phoneNumber, $sessionId, $text);
        
        // Process the request and get response
        $response = $ussdHandler->handle();

        // Log the response for debugging
        Log::info('USSD Response', [
            'session_id' => $sessionId,
            'phone' => $phoneNumber,
            'response' => $response
        ]);

        // Return response with proper headers
        return response($response)
            ->header('Content-Type', 'text/plain');
    }
}
