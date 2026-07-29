<?php

namespace Config;

use App\Libraries\FirebaseAuth;
use App\Libraries\SimilarityCalculator;
use App\Libraries\TextPreprocessor;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function firebaseAuth(bool $getShared = true): FirebaseAuth
    {
        if ($getShared) {
            return static::getSharedInstance('firebaseAuth');
        }
        return new FirebaseAuth();
    }

    public static function textPreprocessor(bool $getShared = true): TextPreprocessor
    {
        if ($getShared) {
            return static::getSharedInstance('textPreprocessor');
        }
        return new TextPreprocessor();
    }

    public static function similarityCalculator(bool $getShared = true): SimilarityCalculator
    {
        if ($getShared) {
            return static::getSharedInstance('similarityCalculator');
        }
        return new SimilarityCalculator();
    }
}
