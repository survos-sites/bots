<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetTranscription
{

    public function __construct(
        protected HttpClientInterface $client
    )
    {
    }

    public function __invoke(string $video_url)
    {

        $this->client = new Client([
            'base_uri' => 'https://api.supadata.ai/v1/youtube/',
            'headers' => [
                'x-api-key' => $_ENV['SUPADATA_API_KEY'],
            ]
        ]);

        $response = $this->client->get('transcript?url=' . $video_url.'&text=true');

        if ($response->getStatusCode() !== 200) {
            return "Transcription APIs error: {$response->getBody()->getContents()}";
        }

        $response = json_decode($response->getBody()->getContents(), true);

        return $response['content'];
    }
}
