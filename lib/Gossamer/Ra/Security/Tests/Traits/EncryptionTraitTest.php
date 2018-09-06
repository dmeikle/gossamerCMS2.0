<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/28/2018
 * Time: 11:30 AM
 */

namespace Gossamer\Ra\Security\Tests\Traits;


use Gossamer\Ra\Security\Traits\EncryptionTrait;
use tests\BaseTest;

class EncryptionTraitTest extends BaseTest
{

    use EncryptionTrait;

    const STRING_TO_ENCRYPT = 'this is the string to encrypt';

    public function testEncrypt() {

        $result = $this->encrypt(self::STRING_TO_ENCRYPT,'','thisisthesalt');
        $this->assertNotEquals($result, self::STRING_TO_ENCRYPT);

        return $result;
    }

    /**
     * @depends testEncrypt
     */
    public function testDecrypt($text) {

        $result = $this->decrypt($text,'','thisisthesalt');
        $this->assertEquals($result, self::STRING_TO_ENCRYPT);

    }
}