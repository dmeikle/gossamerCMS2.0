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
 * Date: 2/8/2018
 * Time: 8:22 PM
 */

namespace Gossamer\Ra\Security\Traits;

use Jose\Component\Core\JWK;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256CBCHS512;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEBuilder;
use Jose\Component\Encryption\JWEDecrypter;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\Encryption\Serializer\JWESerializerManager;

trait EncryptionTrait
{

    protected function encrypt(string $data, string $key, string $salt){
        // The key encryption algorithm manager with the A256KW algorithm.
        $keyEncryptionAlgorithmMgr = AlgorithmManager::create(array(new A256KW()));
        // The content encryption algorithm manager with the A256CBC-HS256 algorithm.
        $contentEncryptionAlgorithmMgr = AlgorithmManager::create([new A256CBCHS512()]);
        // The compression method manager with the DEF (Deflate) method.
        $compressionMethodMgr = CompressionMethodManager::create([new Deflate()]);

        $jsonConverter = new StandardConverter();

        // We instantiate our JWE Builder.
        $jweBuilder = new JWEBuilder(
            $jsonConverter,
            $keyEncryptionAlgorithmMgr,
            $contentEncryptionAlgorithmMgr,
            $compressionMethodMgr
        );
        $jwk = JWK::create([
            'kty' => 'oct',
            'k' => substr(hash('sha256', $salt . $key . $salt), 0, 32),
        ]);
        $jwe = $jweBuilder
            ->create()              // We want to create a new JWE
            ->withPayload($data) // We set the payload
            ->withSharedProtectedHeader([
                'alg' => 'A256KW',        // Key Encryption Algorithm
                'enc' => 'A256CBC-HS512', // Content Encryption Algorithm
                'zip' => 'DEF'            // We enable the compression (irrelevant as the payload is small, just for the example).
            ])
            ->addRecipient($jwk)    // We add a recipient (a shared key or public key).
            ->build();              // We build it
        $serializer = new CompactSerializer($jsonConverter);
        $token = $serializer->serialize($jwe, 0); // We serialize the recipient at index 0 (we only have one recipient).

        return $token;
    }

    private function decrypt($data, $key, $salt) {

        // The key encryption algorithm manager with the A256KW algorithm.
        $keyEncryptionAlgorithmMgr = AlgorithmManager::create(array(new A256KW()));
        // The content encryption algorithm manager with the A256CBC-HS256 algorithm.
        $contentEncryptionAlgorithmMgr = AlgorithmManager::create([new A256CBCHS512()]);
        // The compression method manager with the DEF (Deflate) method.
        $compressionMethodMgr = CompressionMethodManager::create([new Deflate()]);

        $jweDecrypter = new JWEDecrypter(
            $keyEncryptionAlgorithmMgr,
            $contentEncryptionAlgorithmMgr,
            $compressionMethodMgr
        );
        $jwk = JWK::create([
            'kty' => 'oct',
            'k' => substr(hash('sha256', $salt . $key . $salt), 0, 32),
        ]);

        $jsonConverter = new StandardConverter();
        $serializerManager = JWESerializerManager::create([new CompactSerializer($jsonConverter)]);

        $jwe = $serializerManager->unserialize($data);

        $jweDecrypter->decryptUsingKey($jwe, $jwk, 0);

        return $jwe->getPayload();
    }
}