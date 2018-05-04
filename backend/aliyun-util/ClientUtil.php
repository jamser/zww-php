<?php
/*
 * Copyright (c) 2014-2016 Alibaba Group. All rights reserved.
 * License-Identifier: Apache-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
include_once '../aliyun-iot/aliyun-php-sdk-core/Config.php';
class ClientUtil
{
    public static function createClient()
    {
        $accessKeyID = "LTAIiRG3VWVjAIpU";
        $accessSecret = "W78XeKUnB6Er9mFRPTIi1x1wjFCXiX";
        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyID, $accessSecret);

        $client = new DefaultAcsClient($iClientProfile);
        return $client;
    }
}