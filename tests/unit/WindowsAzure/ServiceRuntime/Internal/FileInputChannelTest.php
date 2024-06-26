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
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace Tests\unit\WindowsAzure\ServiceRuntime\Internal;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use WindowsAzure\ServiceRuntime\Internal\ChannelNotAvailableException;
use WindowsAzure\ServiceRuntime\Internal\FileInputChannel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class FileInputChannel.
 *
 * @category  Microsoft
 *
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @version   Release: 0.5.0_2016-11
 *
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class FileInputChannelTest extends TestCase
{
    /**
     * @covers \WindowsAzure\ServiceRuntime\Internal\FileInputChannel::getInputStream
     * @covers \WindowsAzure\ServiceRuntime\Internal\FileInputChannel::closeInputStream
     */
    public function testGetInputStream()
    {
        $rootDirectory = 'root';
        $fileName = 'test.txt';
        $fileContent = 'somecontent';

        // Setup
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory($rootDirectory));

        $file = vfsStream::newFile($fileName);
        $file->setContent($fileContent);

        vfsStreamWrapper::getRoot()->addChild($file);

        // Test
        $fileInputChannel = new FileInputChannel();
        $inputStream = $fileInputChannel->getInputStream(vfsStream::url($rootDirectory.'/'.$fileName));

        $inputChannelContents = stream_get_contents($inputStream);
        $this->assertEquals($fileContent, $inputChannelContents);

        $fileInputChannel->closeInputStream();

        $this->expectException(get_class(new ChannelNotAvailableException()));
        $fileInputChannel->getInputStream(vfsStream::url($rootDirectory.'/'.'fakeinput'));
    }
}
