<?php

namespace BullSheet\Generator;

/*
 * LingTalfi 2016-02-10
 */
use Bat\LocalHostTool;
use BullSheet\Tool\CharGeneratorTool;
use DirScanner\YorgDirScannerTool;

class AuthorBullSheetGenerator extends BullSheetGenerator
{
    
    //------------------------------------------------------------------------------/
    // GENERATED DATA
    //------------------------------------------------------------------------------/
    public function boolean(int $chanceOfGettingTrue = 50): bool
    {
        return mt_rand(1, 100) <= $chanceOfGettingTrue ? true : false;
    }

    public function password(int $length = 10): string
    {
        return $this->asciiChars($length);
    }

    public function numbers(int $length = 3): string
    {
        return CharGeneratorTool::numbers($length);
    }

    public function letters(int $length = 3): string
    {
        return CharGeneratorTool::letters($length);
    }

    public function alphaNumericChars(int $length = 3): string
    {
        return CharGeneratorTool::alphaNumericChars($length);
    }

    public function wordChars(int $length = 3): string
    {
        return CharGeneratorTool::wordChars($length);
    }

    public function asciiChars(int $length = 3): string
    {
        return CharGeneratorTool::asciiChars($length);
    }

}
