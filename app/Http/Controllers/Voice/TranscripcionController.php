<?php

namespace App\Http\Controllers\Voice; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranscripcionController extends Controller
{
    public function procesarAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file',
        ]);

        $audioFile = $request->file('audio');

        try {
            $region = env('VOICE_SERVICE_REGION');
            $endpoint = "https://{$region}.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language=es-ES";

            $audioContent = file_get_contents($audioFile->getRealPath());

            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => env('VOICE_SPEECH_KEY'),
                'Content-Type' => 'audio/webm; codecs=opus', 
                'Accept' => 'application/json'
            ])
            ->send('POST', $endpoint, [
                'body' => $audioContent
            ]);

            if ($response->failed()) {
                Log::error('Error en Azure Speech: ' . $response->body());
                return response()->json(['error' => 'No se pudo transcribir el audio en Azure.'], 500);
            }

            $data = $response->json();
            $textoTranscrito = $data['DisplayText'] ?? '';

            // Se devuelve la data raw para debuggear la respuesta de Azure en el frontend
            return response()->json([
                'texto' => $textoTranscrito,
                'azure_raw' => $data 
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción al transcribir: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno en el servidor.'], 500);
        }
    }
}