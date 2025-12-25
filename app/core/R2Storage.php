<?php

/**
 * R2Storage - Cloudflare R2 Storage with AWS Signature V4 Authentication
 * 
 * Simple implementation using cURL without any external libraries.
 */
class R2Storage
{
    private string $accessKey;
    private string $secretKey;
    private string $bucket;
    private string $endpoint;
    private string $publicUrl;
    private string $region = 'auto'; // R2 uses 'auto' as region

    public function __construct()
    {
        $config = require __DIR__ . '/r2.php';
        
        $this->accessKey = $config['access_key'];
        $this->secretKey = $config['secret_key'];
        $this->bucket = $config['bucket'];
        $this->endpoint = $config['endpoint'];
        $this->publicUrl = $config['public_url'];
    }

    /**
     * Upload a file to R2
     * 
     * @param string $filePath Local file path
     * @param string $objectKey The key (path) in R2
     * @param string $contentType MIME type
     * @return array ['success' => bool, 'url' => string|null, 'error' => string|null]
     */
    public function uploadFile(string $filePath, string $objectKey, string $contentType): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'url' => null, 'error' => 'File not found'];
        }

        $fileContent = file_get_contents($filePath);
        return $this->putObject($objectKey, $fileContent, $contentType);
    }

    /**
     * Upload content directly to R2
     * 
     * @param string $objectKey The key (path) in R2
     * @param string $content File content
     * @param string $contentType MIME type
     * @return array ['success' => bool, 'url' => string|null, 'error' => string|null]
     */
    public function putObject(string $objectKey, string $content, string $contentType): array
    {
        $host = parse_url($this->endpoint, PHP_URL_HOST);
        $url = $this->endpoint . '/' . $this->bucket . '/' . ltrim($objectKey, '/');
        
        $contentHash = hash('sha256', $content);
        $datetime = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        
        $headers = [
            'Host' => $host,
            'Content-Type' => $contentType,
            'Content-Length' => strlen($content),
            'x-amz-content-sha256' => $contentHash,
            'x-amz-date' => $datetime,
        ];

        // Create canonical request
        $canonicalUri = '/' . $this->bucket . '/' . ltrim($objectKey, '/');
        $canonicalQueryString = '';
        
        // Sort headers by lowercase key
        $sortedHeaders = [];
        foreach ($headers as $key => $value) {
            $sortedHeaders[strtolower($key)] = trim($value);
        }
        ksort($sortedHeaders);
        
        $canonicalHeaders = '';
        $signedHeaders = [];
        foreach ($sortedHeaders as $key => $value) {
            $canonicalHeaders .= $key . ':' . $value . "\n";
            $signedHeaders[] = $key;
        }
        $signedHeadersStr = implode(';', $signedHeaders);
        
        $canonicalRequest = "PUT\n" .
            $canonicalUri . "\n" .
            $canonicalQueryString . "\n" .
            $canonicalHeaders . "\n" .
            $signedHeadersStr . "\n" .
            $contentHash;

        // Create string to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = $algorithm . "\n" .
            $datetime . "\n" .
            $credentialScope . "\n" .
            hash('sha256', $canonicalRequest);

        // Calculate signature
        $signingKey = $this->getSigningKey($date);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        // Create authorization header
        $authorization = $algorithm . ' ' .
            'Credential=' . $this->accessKey . '/' . $credentialScope . ', ' .
            'SignedHeaders=' . $signedHeadersStr . ', ' .
            'Signature=' . $signature;

        // Build cURL headers
        $curlHeaders = [
            'Host: ' . $host,
            'Content-Type: ' . $contentType,
            'Content-Length: ' . strlen($content),
            'x-amz-content-sha256: ' . $contentHash,
            'x-amz-date: ' . $datetime,
            'Authorization: ' . $authorization,
        ];

        // Execute request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_PUT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_TIMEOUT => 30,
            // Note: Set to true in production with proper CA bundle
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($error) {
            return ['success' => false, 'url' => null, 'error' => 'cURL error: ' . $error];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            $publicUrl = $this->publicUrl . '/' . ltrim($objectKey, '/');
            return ['success' => true, 'url' => $publicUrl, 'error' => null];
        }

        return [
            'success' => false, 
            'url' => null, 
            'error' => 'Upload failed with HTTP ' . $httpCode . ': ' . $response
        ];
    }

    /**
     * Delete an object from R2
     * 
     * @param string $objectKey The key (path) in R2
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function deleteObject(string $objectKey): array
    {
        $host = parse_url($this->endpoint, PHP_URL_HOST);
        $url = $this->endpoint . '/' . $this->bucket . '/' . ltrim($objectKey, '/');
        
        $contentHash = hash('sha256', ''); // Empty body for DELETE
        $datetime = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        
        $headers = [
            'Host' => $host,
            'x-amz-content-sha256' => $contentHash,
            'x-amz-date' => $datetime,
        ];

        // Create canonical request
        $canonicalUri = '/' . $this->bucket . '/' . ltrim($objectKey, '/');
        $canonicalQueryString = '';
        
        $sortedHeaders = [];
        foreach ($headers as $key => $value) {
            $sortedHeaders[strtolower($key)] = trim($value);
        }
        ksort($sortedHeaders);
        
        $canonicalHeaders = '';
        $signedHeaders = [];
        foreach ($sortedHeaders as $key => $value) {
            $canonicalHeaders .= $key . ':' . $value . "\n";
            $signedHeaders[] = $key;
        }
        $signedHeadersStr = implode(';', $signedHeaders);
        
        $canonicalRequest = "DELETE\n" .
            $canonicalUri . "\n" .
            $canonicalQueryString . "\n" .
            $canonicalHeaders . "\n" .
            $signedHeadersStr . "\n" .
            $contentHash;

        // Create string to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = $algorithm . "\n" .
            $datetime . "\n" .
            $credentialScope . "\n" .
            hash('sha256', $canonicalRequest);

        // Calculate signature
        $signingKey = $this->getSigningKey($date);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        // Create authorization header
        $authorization = $algorithm . ' ' .
            'Credential=' . $this->accessKey . '/' . $credentialScope . ', ' .
            'SignedHeaders=' . $signedHeadersStr . ', ' .
            'Signature=' . $signature;

        // Build cURL headers
        $curlHeaders = [
            'Host: ' . $host,
            'x-amz-content-sha256: ' . $contentHash,
            'x-amz-date: ' . $datetime,
            'Authorization: ' . $authorization,
        ];

        // Execute request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_TIMEOUT => 30,
            // Note: Set to true in production with proper CA bundle
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($error) {
            return ['success' => false, 'error' => 'cURL error: ' . $error];
        }

        // 204 No Content is success for DELETE
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'error' => null];
        }

        return [
            'success' => false, 
            'error' => 'Delete failed with HTTP ' . $httpCode . ': ' . $response
        ];
    }

    /**
     * Generate the signing key for AWS Signature V4
     */
    private function getSigningKey(string $date): string
    {
        $kDate = hash_hmac('sha256', $date, 'AWS4' . $this->secretKey, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        return $kSigning;
    }

    /**
     * Get the public URL for an object
     */
    public function getPublicUrl(string $objectKey): string
    {
        return $this->publicUrl . '/' . ltrim($objectKey, '/');
    }

    /**
     * Extract object key from a public URL
     */
    public function getObjectKeyFromUrl(string $url): ?string
    {
        if (strpos($url, $this->publicUrl) === 0) {
            return ltrim(substr($url, strlen($this->publicUrl)), '/');
        }
        return null;
    }
}
