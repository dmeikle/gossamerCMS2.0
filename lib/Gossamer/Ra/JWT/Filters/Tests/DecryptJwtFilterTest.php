<?php
/**
 * Created by PhpStorm.
 * User: Travis Junck
 * Date: 2019-11-16
 * Time: 5:54 PM
 */

namespace lib\Gossamer\Ra\JWT\Filters\Tests;


use Gossamer\Essentials\Configuration\SiteParams;
use Gossamer\Horus\Filters\FilterConfig;
use Gossamer\Horus\Http\HttpRequest;
use Gossamer\Horus\Http\RequestParams;
use Gossamer\Ra\JWT\Filters\DecryptJwtFilter;
use Gossamer\Ra\JWT\TokenManager;
use tests\BaseTest;

class DecryptJwtFilterTest extends BaseTest
{

    public function testJwTDecrypt() {
        $jwt='eyJhbGciOiJBMjU2S1ciLCJlbmMiOiJBMjU2Q0JDLUhTNTEyIiwiemlwIjoiREVGIn0.RyqdrSZCHklAajeEoTZOd8-mWmP14zlXSuQRMY2RapI3GFFDH1zG0L3WuP1Xq9eCuU0WjsHJQBLo_jn90AosIeowk6vXaD7f.yMJevFLltf45-Kiw6ornxw.8fEBCErdSjSu5NoptfDqX0t7GMDrhnnR3RMVPq41mMxlmJ_6I4uvztMxQhMQ_5Lax0lH4iNZ-foXTw9Ahvv8k-rpP5jHkkzdg6Ollhii7PgDCzHZyzGYwrFZmbT9G_s3.geSFqLeEdXENaRn7i2UBsBPU3HkskjfzJil14-5OE-g';
        $httpRequest = new HttpRequest(new RequestParams(), new SiteParams());

        $manager = new TokenManager($httpRequest);

        $item = $manager->getDecryptedJwtToken($jwt);

        pr($item);
    }
}