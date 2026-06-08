<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function procesarMensaje(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000'
        ]);

        $mensajeUsuario = trim($request->mensaje);
        $historial = $request->input('historial', []);

        $rutaReglas = storage_path('app/data/reglas_agente.txt');
        $rutaInfo = storage_path('app/data/informacion.txt');

        $reglas = file_exists($rutaReglas) ? file_get_contents($rutaReglas) : '';
        $informacion = file_exists($rutaInfo) ? file_get_contents($rutaInfo) : '';

        Log::info('VERIFICACIÓN NATIVA DE PHP', [
            'ruta_info' => $rutaInfo,
            'existe_info_php' => file_exists($rutaInfo),
        ]);
        Log::info('TXT CARGADOS', [
            'reglas_length' => strlen($reglas),
            'informacion_length' => strlen($informacion),
        ]);
        $baseSystem = "
        Eres el asistente virtual oficial de Ingeniería Líder.

        Debes:
        - responder de forma profesional y clara,
        - usar únicamente la información del contexto,
        - interpretar preguntas relacionadas inteligentemente,
        - hacer preguntas aclaratorias si algo es ambiguo,
        - evitar inventar información,
        - responder de manera breve y útil,
        - variar la forma de responder naturalmente,
        - evitar respuestas repetitivas o robóticas.

        IMPORTANTE:
        - No uses siempre las mismas frases.
        - No termines todas las respuestas igual.
        - No repitas constantemente:
            'Si deseas más información...'
            'No dudes en escribirme...'
            'Estoy aquí para ayudarte...'

        Las respuestas deben sentirse:
        - naturales,
        - humanas,
        - conversacionales,
        - profesionales.

        A veces:
        - responde solo lo necesario,
        - a veces agrega una recomendación,
        - a veces realiza una pregunta,
        - y otras veces responde de forma directa.
        ";

        $systemPrompt = <<<EOT
{$baseSystem}

==================================
REGLAS DEL AGENTE
==================================

{$reglas}

==================================
CONTEXTO OFICIAL
==================================

{$informacion}
EOT;

        // LOG 4: Ver el System Prompt FINAL que se enviará a la IA
        Log::info('SYSTEM PROMPT FINAL', [
            'prompt' => $systemPrompt
        ]);

        $url =
            rtrim(env('ENDPOINT'), '/') .
            '/openai/deployments/' .
            env('DEPLOYMENT_NAME') .
            '/chat/completions?api-version=' .
            env('API_VERSION');

        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ]
            ];

            foreach ($historial as $msg) {
                if (!isset($msg['rol']) || !isset($msg['texto'])) {
                    continue;
                }

                $messages[] = [
                    'role' => $msg['rol'] === 'usuario'
                        ? 'user'
                        : 'assistant',
                    'content' => $msg['texto'],
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $mensajeUsuario,
            ];

            $response = Http::withHeaders([
                'api-key' => env('AZURE_AI_KEY'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, [
                    'messages' => $messages,
                    'temperature' => 0.5,
                    'max_tokens' => 500,
                    'presence_penalty' => 0.1,
                    'frequency_penalty' => 0.5,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'respuesta' => $response->json('choices.0.message.content'),
                ]);
            }

            Log::error('Error Azure OpenAI', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Servicio no disponible.',
            ], 500);

        } catch (\Exception $e) {

            Log::error('Error Chatbot', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error de conexión.',
            ], 500);
        }
    }
}