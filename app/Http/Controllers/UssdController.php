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
        try {
            // Handle GET requests (for testing)
            if ($request->isMethod('GET')) {
                return response("CON USSD Callback URL Working!\nThis endpoint accepts POST requests from Africa's Talking.\nTest your USSD code by dialing it on your phone.")
                    ->header('Content-Type', 'text/plain')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Validate required parameters
            $sessionId = $request->input('sessionId');
            $serviceCode = $request->input('serviceCode');
            $phoneNumber = $request->input('phoneNumber');
            $text = $request->input('text', ''); // Default to empty string

            if (!$sessionId || !$phoneNumber) {
                Log::warning('USSD Callback missing required parameters', $request->all());
                return response("END Invalid request parameters.")
                    ->header('Content-Type', 'text/plain');
            }

            // Log the incoming request for debugging
            Log::info('USSD Callback received', [
                'session_id' => $sessionId,
                'service_code' => $serviceCode,
                'phone' => $phoneNumber,
                'text' => $text,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Create USSD handler instance with timeout protection
            $ussdHandler = new UssdHandler($phoneNumber, $sessionId, $text);
            
            // Process the request and get response with timeout
            $response = $ussdHandler->handle();

            // Ensure response is valid
            if (empty($response) || !is_string($response)) {
                Log::error('USSD Handler returned invalid response', [
                    'session_id' => $sessionId,
                    'phone' => $phoneNumber,
                    'response_type' => gettype($response)
                ]);
                $response = "END System error. Please try again later.";
            }

            // Log the response for debugging
            Log::info('USSD Response', [
                'session_id' => $sessionId,
                'phone' => $phoneNumber,
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 50) . (strlen($response) > 50 ? '...' : '')
            ]);

            // Return response with proper headers to prevent caching issues
            return response($response)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('Connection', 'close'); // Ensure connection closes properly

        } catch (\Exception $e) {
            Log::error('USSD Callback Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return safe error response
            return response("END Service temporarily unavailable. Please try again.")
                ->header('Content-Type', 'text/plain')
                ->header('Connection', 'close');
        }
    }
}
