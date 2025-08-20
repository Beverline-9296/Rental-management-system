<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UssdTestController extends Controller
{
    /**
     * Simple test endpoint to verify USSD connectivity
     */
    public function test(Request $request)
    {
        Log::info('USSD Test endpoint hit', $request->all());
        
        return response("CON USSD Test Successful\nThis is a test response")
            ->header('Content-Type', 'text/plain');
    }
    
    /**
     * Echo back the request data for debugging
     */
    public function debug(Request $request)
    {
        $data = [
            'sessionId' => $request->input('sessionId'),
            'serviceCode' => $request->input('serviceCode'), 
            'phoneNumber' => $request->input('phoneNumber'),
            'text' => $request->input('text'),
            'all_data' => $request->all()
        ];
        
        Log::info('USSD Debug Data', $data);
        
        $response = "END DEBUG INFO\n";
        $response .= "Phone: " . $request->input('phoneNumber') . "\n";
        $response .= "Text: '" . $request->input('text') . "'\n";
        $response .= "Session: " . $request->input('sessionId');
        
        return response($response)
            ->header('Content-Type', 'text/plain');
    }
}
