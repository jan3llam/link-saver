<?php

namespace App\Services;

use App\Models\Link;
use App\Spiders\MetadataSpider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class LinkService {

    /**
     * Save metadata from a given URL.
     *
     * @param array $data
     * @return Link|null
     */
    public function saveLinkMetadata(array $data): ?Link {
        try {
            Log::info("Fetching metadata for URL: {$data['url']}");

            $fetchedData = Roach::collectSpider(
                MetadataSpider::class,
                new Overrides(startUrls: [$data['url']])
            )[0] ?? [];

            if (empty($fetchedData)) {
                Log::warning("No metadata found for URL: {$data['url']}");
            }

            $link = Link::create([
                'url'         => $data['url'],
                'subject'     => $data['subject'],
                'sender'      => $data['sender'],
                'title'       => $fetchedData['title'] ?? 'No Title',
                'description' => $fetchedData['description'] ?? 'No Content',
            ]);

            Log::info("Metadata saved successfully for URL: {$data['url']}");

            return $link;
        } catch (\Exception $e) {
            Log::error("Error fetching metadata for URL: {$data['url']}. Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all saved links, ordered by newest first.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function userLinks(?string $userEmail):Collection {

        $query = Link::orderBy('created_at', 'desc');

        if ($userEmail) {
            $query->where('sender', $userEmail);
        }

        return $query->get();
    }
}
