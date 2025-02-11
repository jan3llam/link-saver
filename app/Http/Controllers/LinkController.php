<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\Services\LinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller {
    protected LinkService $linkService;
    protected EmailService $emailService;

    public function __construct(
        LinkService $linkService,
        EmailService $emailService
     ) {
        $this->linkService = $linkService;
        $this->emailService = $emailService;
    }

    /**
     * Fetch emails from inbox and process links.
     */
    public function fetchMail(): JsonResponse {
        try {
            $response = $this->emailService->processInbox();

            if ($response->getStatusCode() === 200) {
                return response()->json([
                    'message' => 'Emails fetched successfully',
                    'data' => $response->getData(),
                ], 200);
            }

            return response()->json([
                'message' => 'Failed to fetch emails',
                'error' => $response->getData(),
            ], $response->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Error fetching emails: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch emails',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Retrieve all saved links for a specific email
     */
    public function getUserLinks(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->query(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid email address',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $links = $this->linkService->userLinks($request->query('email'));

            return response()->json([
                'data' => $links,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching links: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch links',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
