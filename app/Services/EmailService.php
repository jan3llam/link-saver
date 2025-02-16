<?php

namespace App\Services;

use Webklex\IMAP\Facades\Client;
use App\Services\LinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EmailService {

    public function __construct(
        private LinkService $linkService
        ) {}

    /**
     * Process unread emails, extract URLs, and save metadata.
     *
     * @return JsonResponse
     */
    public function processInbox(): JsonResponse
    {
        try {
            Log::info("Connecting to email account...");

            $client = Client::account('default');
            $client->connect();
            $inbox = $client->getFolder('INBOX');

            Log::info("Fetching unread messages...");

            foreach ($inbox->messages()->unseen()->get() as $message) {
                $content = $message->getTextBody();

                preg_match_all('/https:\/\/[^\s]+/i', $content, $matches);
                $urls = array_unique($matches[0] ?? []);

                if ($urls) {
                    Log::info("Found " . count($urls) . " URLs in an email.");

                    foreach ($urls as $url) {
                        $url = rtrim($url, '.,;!?()[]{}');

                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                            Log::warning("Invalid URL detected and skipped: {$url}");
                            continue;
                        }


                        $sender = filter_var($message->getFrom()[0]->mail ?? 'Unknown Sender', FILTER_SANITIZE_EMAIL);
                        $subject = substr($message->getSubject(), 0, 255);

                        $data = [
                            'sender'   => $sender,
                            'subject'  => $subject,
                            'url'      => $url
                        ];

                        $this->linkService->saveLinkMetadata($data);
                    }
                }

                $message->setFlag('Seen');
            }

            Log::info("Inbox processing completed.");

            return response()->json([
                'message' => 'Inbox processed successfully',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error processing inbox: " . $e->getMessage());

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
