<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 *
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Internal\Http;

use Mail_mimePart;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;

/**
 * Batch request marshaller.
 *
 * @category  Microsoft
 *
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @version   Release: 0.5.0_2016-11
 *
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchRequest
{
    /**
     * Http call context list.
     *
     * @var HttpCallContext[]
     */
    private $_contexts = [];

    /**
     * Headers.
     *
     * @var array
     */
    private $_headers;

    /**
     * Request body.
     *
     * @var string
     */
    private $_body;

    /**
     * Append new context to batch request.
     *
     * @param HttpCallContext $context Http call context to add to batch request
     */
    public function appendContext(HttpCallContext $context)
    {
        $this->_contexts[] = $context;
    }

    /**
     * Encode contexts.
     */
    public function encode()
    {
        $mimeType = Resources::MULTIPART_MIXED_TYPE;
        $batchGuid = Utilities::getGuid();
        $batchId = sprintf('batch_%s', $batchGuid);
        $contentType1 = ['content_type' => "$mimeType"];
        $changeSetGuid = Utilities::getGuid();
        $changeSetId = sprintf('changeset_%s', $changeSetGuid);
        $contentType2 = ['content_type' => "$mimeType; boundary=$changeSetId"];
        $options = [
            'encoding' => 'binary',
            'content_type' => Resources::HTTP_TYPE,
        ];

        // Create changeset MIME part
        $changeSet = new Mail_mimePart();

        $i = 1;
        foreach ($this->_contexts as $context) {
            $context->addHeader(Resources::CONTENT_ID, $i);
            $changeSet->addSubpart((string) $context, $options);

            ++$i;
        }

        // Encode the changeset MIME part
        $changeSetEncoded = $changeSet->encode($changeSetId);

        // Create the batch MIME part
        $batch = new Mail_mimePart(Resources::EMPTY_STRING, $contentType1);

        // Add changeset encoded to batch MIME part
        $batch->addSubpart($changeSetEncoded['body'], $contentType2);

        // Encode batch MIME part
        $batchEncoded = $batch->encode($batchId);
        foreach ($batchEncoded['headers'] as $key => $value) {
            $this->_headers[$key] = str_replace("\r\n", '', $value);
        }
        $this->_body = $batchEncoded['body'];
    }

    /**
     * Get "Request body".
     *
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Get "Headers".
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get request contexts.
     *
     * @return array
     */
    public function getContexts()
    {
        return $this->_contexts;
    }
}
