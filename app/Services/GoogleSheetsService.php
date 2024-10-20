<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\File;
class GoogleSheetsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('New line');
        // $this->client->setScopes(Google_service_Sheets::Spre;
        $this->client->setAuthConfig(storage_path('app/secret.json'));

        // $this->client->setAccessType('offline');
        // $this->client->setScopes([
        //     'https://www.googleapis.com/auth/spreadsheets',
        // ]);
        
        // $this->client->setClientId('33444722757-n99np8prkoh9ipc4re5spss7o6l7bthh.apps.googleusercontent.com');
        // $this->client->setClientSecret('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');
        // // $this->client->setRedirectUri('http://localhost:8088//google/callback'); // Replace with your actual callback URL
        $this->client->setAccessType('offline');
        // $this->client->setScopes([
        //     'https://www.googleapis.com/auth/spreadsheets',
        // ]);

        // $contents = File::get(storage_path('app/credentials.json'));
        // $json = json_decode($contents, true);
        // dd($this->client);
    }

    public function read()
    {
        $client = new Client();
        $client->setApplicationName('New line');
        $client->setAuthConfig(storage_path('app/secret.json'));
        $spreadsheetId = '1_Ojhv00-GsA5KcjpVI2xd0hnvKk7-ruT1pZ3gEQmY6E';
        $range = 'trellotime!A1:D';
        // $client->useApplicationDefaultCredentials();
        $service = new Sheets($client);

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
            // Handle the retrieved values as needed
            dd($values);
        } catch (\Exception $e) {
            // Handle any exceptions, e.g., authentication errors, API access errors
            dd($e->getMessage());
        }
        return $response->getValues();
    }

    public function write($data)
    {
        // Implement logic to write to Google Sheets
        // Example:
        // $service = new Sheets($this->client);
        // $body = new \Google\Service\Sheets\ValueRange(['values' => $data]);
        // $params = ['valueInputOption' => 'RAW'];
        // $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }
}
