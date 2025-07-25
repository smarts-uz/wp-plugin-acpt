<?php

use VendorDuplicator\Google_Client;
use VendorDuplicator\Google_Exception;
use VendorDuplicator\Google_Http_Request;
use VendorDuplicator\Google_Http_REST;
use VendorDuplicator\Google_Utils;

defined("ABSPATH") or die("");
/**
 * Copyright 2012 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!class_exists('DUP_Pro_EnhancedGoogleMediaFileUpload')) {
    /**
     * Manage large file uploads, which may be media but can be any type
     * of sizable data.
     */
    class DUP_Pro_EnhancedGoogleMediaFileUpload
    {
        const UPLOAD_MEDIA_TYPE     = 'media';
        const UPLOAD_MULTIPART_TYPE = 'multipart';
        const UPLOAD_RESUMABLE_TYPE = 'resumable';

        /** @var string $mimeType */
        private $mimeType;

        /** @var string $data */
        private $data;

        /** @var bool $resumable */
        private $resumable;

        /** @var int $chunkSize */
        private $chunkSize;

        /** @var int $size */
        private $size;

        /** @var string $resumeUri */
        public $resumeUri;

        /** @var int $next_offset */
        private $next_offset;

        /** @var Google_Client */
        private $client;

        /** @var Google_Http_Request */
        private $request;

        /** @var string */
        private $boundary;

        /**
         * Result code from last HTTP call
         *
         * @var int
         */
        private $httpResultCode;

        /**
         * @param Google_Client       $client    The API client
         * @param Google_Http_Request $request   The HTTP request to be executed.
         * @param $mimeType  string The mime-type of the file
         * @param $data      string The bytes you want to upload.
         * @param $resumable bool True if this is a resumable upload
         * @param $chunkSize int File will be uploaded in chunks of this many bytes only used if resumable=True
         * @param $boundary  string The boundary string
         * @param $progress  int Progress in bytes uploaded
         * @param $resumeUri string The URI to resume at
         */
        public function __construct(
            Google_Client $client,
            Google_Http_Request $request,
            $mimeType,
            $data,
            $resumable = false,
            $chunkSize = 0,
            $boundary = false,
            $progress = 0, // RSR NEW Param - allows continuation across different PHP requests
            $resumeUri = false // RSR NEW Param - allows continuation across different PHP requests
        ) {
            $this->client    = $client;
            $this->request   = $request;
            $this->mimeType  = $mimeType;
            $this->data      = $data;
            $this->size      = strlen($this->data);
            $this->resumable = $resumable;
            $this->resumeUri = $resumeUri;
            if (!$chunkSize) {
                $chunkSize = 256 * 1024;
            }
            $this->chunkSize   = $chunkSize;
            $this->next_offset = $progress;
            $this->boundary    = $boundary;

            // Process Media Request
            $this->process();
        }

        /**
         * Set the size of the file that is being uploaded.
         *
         * @param $size - int file size in bytes
         *
         * @return void
         */
        public function setFileSize($size)
        {
            $this->size = $size;
        }

        /**
         * Return the progress on the upload
         *
         * @return int progress in bytes uploaded.
         */
        public function getNextOffset()
        {
            return $this->next_offset;
        }

        /**
         * Return the HTTP result code from the last call made.
         *
         * @return int code
         */
        public function getHttpResultCode()
        {
            return $this->httpResultCode;
        }

        /**
         * Send the next part of the file to upload.
         *
         * @param bool|string $chunk Chunk of file
         *
         * @return false|mixed|null
         *
         * @throws Google_Exception
         * @throws Google_IO_Exception
         * @throws Google_Service_Exception
         */
        public function nextChunk($chunk = false)
        {
            DUP_PRO_Log::trace('Next chunk');

            if (false == $this->resumeUri) {
                $this->resumeUri = $this->getResumeUri();
            }

            if (false == $chunk) {
                $chunk = substr($this->data, $this->next_offset, $this->chunkSize);
            }

            $lastBytePos = $this->next_offset + strlen($chunk) - 1;
            $headers     = array(
                'content-range'  => "bytes $this->next_offset-$lastBytePos/$this->size",
                'content-type'   => $this->request->getRequestHeader('content-type'),
                'content-length' => $this->chunkSize,
                'expect'         => '',
            );

            DUP_PRO_Log::traceObject('next chunk headers', $headers);

            $httpRequest = new Google_Http_Request(
                $this->resumeUri,
                'PUT',
                $headers,
                $chunk
            );

            if ($this->client->getClassConfig("Google_Http_Request", "enable_gzip_for_uploads")) {
                $httpRequest->enableGzip();
            } else {
                $httpRequest->disableGzip();
            }

            $response = $this->client->getIo()->makeRequest($httpRequest);
            $response->setExpectedClass($this->request->getExpectedClass());
            $code                 = $response->getResponseHttpCode();
            $this->httpResultCode = $code;

            if (308 == $code) {
                // Track the amount uploaded.
                $range             = explode('-', $response->getResponseHeader('range'));
                $this->next_offset = $range[1] + 1;

                // Allow for changing upload URLs.
                $location = $response->getResponseHeader('location');
                if ($location) {
                    $this->resumeUri = $location;
                }

                // No problems, but upload not complete.
                return false;
            } else {
                return Google_Http_REST::decodeHttpResponse($response, $this->client);
            }
        }

        /**
         * @visible for testing
         *
         * @return void
         */
        private function process()
        {
            $postBody    = false;
            $contentType = false;

            $meta = $this->request->getPostBody();
            $meta = is_string($meta) ? json_decode($meta, true) : $meta;

            $uploadType = $this->getUploadType($meta);
            $this->request->setQueryParam('uploadType', $uploadType);
            $this->transformToUploadUrl();
            $mimeType = $this->mimeType ?
                    $this->mimeType :
                    $this->request->getRequestHeader('content-type');

            if (self::UPLOAD_RESUMABLE_TYPE == $uploadType) {
                $contentType = $mimeType;
                $postBody    = is_string($meta) ? $meta : json_encode($meta);
            } elseif (self::UPLOAD_MEDIA_TYPE == $uploadType) {
                $contentType = $mimeType;
                $postBody    = $this->data;
            } elseif (self::UPLOAD_MULTIPART_TYPE == $uploadType) {
                // This is a multipart/related upload.
                $boundary    = $this->boundary ? $this->boundary : mt_rand();
                $boundary    = str_replace('"', '', $boundary);
                $contentType = 'multipart/related; boundary=' . $boundary;
                $related     = "--$boundary\r\n";
                $related    .= "Content-Type: application/json; charset=UTF-8\r\n";
                $related    .= "\r\n" . json_encode($meta) . "\r\n";
                $related    .= "--$boundary\r\n";
                $related    .= "Content-Type: $mimeType\r\n";
                $related    .= "Content-Transfer-Encoding: base64\r\n";
                $related    .= "\r\n" . base64_encode($this->data) . "\r\n";
                $related    .= "--$boundary--";
                $postBody    = $related;
            }

            $this->request->setPostBody($postBody);

            if (isset($contentType) && $contentType) {
                $contentTypeHeader['content-type'] = $contentType;
                $this->request->setRequestHeaders($contentTypeHeader);
            }
        }

        private function transformToUploadUrl()
        {
            $base = $this->request->getBaseComponent();
            $this->request->setBaseComponent($base . '/upload');
        }

        /**
         * Valid upload types:
         * - resumable (UPLOAD_RESUMABLE_TYPE)
         * - media (UPLOAD_MEDIA_TYPE)
         * - multipart (UPLOAD_MULTIPART_TYPE)
         *
         * @param $meta string metadata
         *
         * @return string
         *
         * @visible for testing
         */
        public function getUploadType($meta)
        {
            if ($this->resumable) {
                return self::UPLOAD_RESUMABLE_TYPE;
            }

            if (false == $meta && $this->data) {
                return self::UPLOAD_MEDIA_TYPE;
            }

            return self::UPLOAD_MULTIPART_TYPE;
        }

        private function getResumeUri()
        {
            $result = null;
            $body   = $this->request->getPostBody();
            if ($body) {
                $headers = array(
                    'content-type'            => 'application/json; charset=UTF-8',
                    'content-length'          => Google_Utils::getStrLen($body),
                    'x-upload-content-type'   => $this->mimeType,
                    'x-upload-content-length' => $this->size,
                    'expect'                  => '',
                );
                $this->request->setRequestHeaders($headers);
            }

            $response = $this->client->getIo()->makeRequest($this->request);
            $location = $response->getResponseHeader('location');
            $code     = $response->getResponseHttpCode();

            if (200 == $code && true == $location) {
                return $location;
            }
            $message = $code;
            $body    = @json_decode($response->getResponseBody());
            if (!empty($body->error->errors)) {
                $message .= ': ';
                foreach ($body->error->errors as $error) {
                    $message .= "{$error->domain}, {$error->message};";
                }
                $message = rtrim($message, ';');
            }

            $error = "Failed to start the resumable upload (HTTP {$message})";
            $this->client->getLogger()->error($error);
            throw new Google_Exception($error);
        }
    }

}
