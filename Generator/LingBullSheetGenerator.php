<?php

namespace BullSheet\Generator;

/*
 * LingTalfi 2016-02-10
 * This class assumes that you use the ling bullsheets repository.
 * https://github.com/bullsheet/bullsheets-repo/tree/master/bullsheets/ling
 * 
 */

use BullSheet\Exception\BullSheetException;

class LingBullSheetGenerator extends AuthorBullSheetGenerator
{


    //------------------------------------------------------------------------------/
    // COMBINED DATA
    //------------------------------------------------------------------------------/
    public function email(): string
    {
        return $this->pseudo() . '@' . $this->getPureData('free_email_provider_domains');
    }

    /**
     * Returns a pseudo, using either a generator (lots of variations),
     * or a pure data stream (1932 variations).
     */
    public function pseudo(bool $useGenerator = true): string
    {
        if (true === $useGenerator) {


            /**
             * Rules used to generate the random pseudo
             * -----------------------------------
             *
             * A generated pseudo uses the following components:
             *
             * - first name
             * - last name
             * - pseudo (pure data)
             * - number
             *
             *
             * The 3 first components are called name components, and the last one is the number component.
             * The generated pseudo contains at least one name component, and at most two name components
             * (i.e, you can never have the three types of name components in a generated pseudo).
             * Most likely (990 times out of 1000), two name components will be used (and not just one).
             *
             * The number component is added most of the time (999 times out of 1000).
             *
             * The order in which components are combined is defined randomly, but it cannot start with
             * the number component.
             *
             * There is a separator char between components; it can be either dash, underscore,
             * empty string (it has been chosen so to be compatible with the locale part of an email address).
             * This separator too is chosen randomly.
             *
             * If the number component is used (and it will probably), then the number's length is
             * a random number between 1 and 6, but the highest the number (6 is the highest), the higher probability
             * it has to get picked (i.e., there is more chance that the number has
             * length 6 than 5, 5 than 4, 4 than 3, and so on).
             *
             */

            $s = '';
            $useNumber = (mt_rand(1, 1000) < 1000);
            $useSecondName = (mt_rand(1, 1000) <= 990);
            $p = [
                'f',
                'l',
                'p',
            ];
            $sep = [
                '_',
                '-',
                '',
            ];

            // start by choosing one name component
            $index = mt_rand(0, 2);
            $letter = $p[$index];
            unset($p[$index]);
            $p = array_values($p);
            $s .= $this->getComponentName($letter);


            $c = [];
            $n = 0;
            if (true === $useNumber) {
                $str = '122333444455555666666';
                $length = $str[mt_rand(0, 20)];
                $c[] = $this->numbers($length);
                $n++;
            }

            if (true === $useSecondName) {
                $index = mt_rand(0, 1);
                $c[] = $this->getComponentName($p[$index]);
                $n++;
            }
            if (2 === $n) {
                shuffle($c);
                $se = $sep[mt_rand(0, 2)];
                $s2 = $sep[mt_rand(0, 2)];
                $suffix = $s2 . implode($se, $c);
            }
            elseif (1 === $n) {
                $se = $sep[mt_rand(0, 2)];
                $suffix = $se . current($c);
            }
            else {
                $suffix = '';
            }
            $s .= $suffix;
        }
        else {
            $s = $this->getPureData('pseudo');
        }


        return $s;
    }

    //------------------------------------------------------------------------------/
    // PURE DATA
    //------------------------------------------------------------------------------/
    public function actor(): string
    {
        return $this->getPureData('actor');
    }

    public function firstName(): string
    {
        return $this->getPureData('first_name');
    }

    public function lastName(): string
    {
        return $this->getPureData('last_name');
    }

    public function topLevelDomain(): string
    {
        return $this->getPureData('top_level_domain');
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getDir(): string
    {
        return parent::getDir() . '/ling';
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function getComponentName(string $choice): string
    {
        switch ($choice) {
            case 'f':
                return $this->firstName();
                break;
            case 'l':
                return $this->lastName();
                break;
            case 'p':
                return $this->getPureData('pseudo');
                break;
            default:
                throw new BullSheetException("Unknown choice: $choice");
                break;
        }
    }
}
