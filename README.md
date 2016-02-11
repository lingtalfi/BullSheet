BullSheet
================
2016-02-09



Generate fake data to populate your database.



Features
--------------

- php7
- easy to extend
- data is decoupled from the generator, you can create your own data directory easily
- existing public data repository 





The example
-----------------

This example showcases what kind of methods you can use.


```php
<?php



use BullSheet\Generator\LingBullSheetGenerator;

require_once "bigbang.php"; // start the local universe



$b = LingBullSheetGenerator::create()->setDir("/path/to/my/bullsheets-repo");


//------------------------------------------------------------------------------/
// PURE DATA
//------------------------------------------------------------------------------/
a($b->getPureData("first_name"));
a($b->getPureData("top_level_domain"));
a($b->getPureData("last_name"));
a($b->getPureData("actor"));


//------------------------------------------------------------------------------/
// AUTHOR SPECIFIC
//------------------------------------------------------------------------------/
a($b->numbers(5));
a($b->letters(5));
a($b->asciiChars(5));
a($b->wordChars(5));
a($b->alphaNumericChars(5));
a($b->password());


//------------------------------------------------------------------------------/
// LING SPECIFIC
//------------------------------------------------------------------------------/
a($b->actor());
a($b->firstName());
a($b->lastName());
a($b->topLevelDomain());
a($b->pseudo());
a($b->email());

```

The code above generates an output like this:

```
string 'yale' (length=4)
string 'xn--s9brj9c' (length=11)
string 'hermie' (length=6)
string 'Movita' (length=6)
string '35421' (length=5)
string 'nUjmp' (length=5)
string 'jQveV' (length=5)
string '8JNA_' (length=5)
string '1JXoE' (length=5)
string 'm6Qf'y[I)m' (length=10)
string 'Rogelio' (length=7)
string 'damiana' (length=7)
string 'valentine' (length=9)
string 'fresenius' (length=9)
string 'NLaguie_58386' (length=13)
string 'dyanbriant-482660@digibel.be' (length=28)

```





The basic idea
-----------------

The basic idea behind BullSheet is that it takes a random line in a file that you create.

Now, to make things simple we create one directory where we put all the data.
This directory is called the **bullsheets** directory, and there should be only one per host (machine).

Also, by convention, all data is put in a .txt file.
 

If you want more details on how it works, and why it works that way, go to the [more verbose README](https://github.com/lingtalfi/BullSheet/blob/master/docs/README_aux.md)
that I first made. 


Follow the tutorial below to have a pragmatic understanding of how it works. 




The tutorial to understand the concepts
-----------------

Alright.
Create the following structure on your local machine.
 
 
``` 
- bullsheets
----- rainbow_color  
--------- data.txt  
```
 
 
Now open bullsheets/rainbow_color/data.txt and put the following content in it:


```
red
orange
yellow
green
blue
indigo
violet
```

Now create a php file (anywhere), and put the following content in it:
 
 
```php 
<?php


use BullSheet\Generator\AuthorBullSheetGenerator;

require_once "bigbang.php"; // start the local universe



$b = AuthorBullSheetGenerator::create()->setDir("/path/to/your/bullsheets");
a($b->getPureData('rainbow_color'));

``` 


The above code will display the name of a rainbow color on your screen.

### Explanations of the code

We first require the [bigbang script, to start the universe](https://github.com/lingtalfi/TheScientist/blob/master/convention.portableAutoloader.eng.md) (be able 
to parse any classes of the universe).

Then we tell tot the BullSheet generator where our **bullsheets** repository is.

And eventually we ask it to get a random line from any data file found in the rainbow_color directory.

There is a lot more that we can do, if you feel curious, there are my [conception notes](https://github.com/lingtalfi/BullSheet/blob/master/docs/README_aux.md).





The tutorial to use LingBullSheetGenerator
-----------------

If you understand the basic principle of the tutorial above, then you might understand LingBullSheetGenerator as well.
LingBullSheetGenerator is a BullSheet generator which bullsheets structure looks like this (as the time of writing):



```
- bullsheets
----- ling   
--------- actor
------------- given_name
----------------- data.txt
----------------- src.md
--------- first_name
------------- all
----------------- data.txt
----------------- src.md
--------- free_email_provider_domains
------------- all
----------------- data.txt
----------------- src.md
--------- iso639-1
------------- all
----------------- data.txt
----------------- src.md
--------- iso639-2
------------- all
----------------- data.txt
----------------- src.md
--------- last_name
------------- international
----------------- all
--------------------- data.txt
--------------------- src.md
----------------- female
--------------------- data.txt
--------------------- src.md
----------------- male
--------------------- data.txt
--------------------- src.md
--------- pseudo
------------- american
----------------- data.txt
----------------- src.md
--------- top_level_domain
------------- all
----------------- data.txt
----------------- src.md
```


Download the data from the [ling bullsheets repository](https://github.com/bullsheet/bullsheets-repo/tree/master/bullsheets/ling),
and place the ling dir into your own local **bullsheets** dir.



Now to get, let's say a first name, you just need to target the first_name directory, like this:

```php
<?php


use BullSheet\Generator\LingBullSheetGenerator;

require_once "bigbang.php"; // start the local universe



$b = LingBullSheetGenerator::create()->setDir("/path/to/your/bullsheets");
a($b->getPureData('first_name'));


```

Notice that we didn't specify the ling directory in the above code, that's because the getPureData method of the 
LingBullSheetGenerator prefixes it automatically for us.


Now in case you wonder, here are more examples.

### get a random female last name

Your domain is really a relative path to a directory.
If you look closely to the structure of the ling/last_name directory, you will see that it contains 3 directories.

By default, if you use a domain of last_name, the generator will pick any of the available data files (randomly),
and return a random line for it.

Now you can more specific and say that your domain is last_name/female, you would then get a random female name.
See how it is done in the example below.


```php
<?php


use BullSheet\Generator\LingBullSheetGenerator;

require_once "bigbang.php"; // start the local universe



$b = LingBullSheetGenerator::create()->setDir("/path/to/your/bullsheets");
a($b->getPureData('last_name/female'));


```


Other possibilities are explored in greater details in the [conception notes](https://github.com/lingtalfi/BullSheet/blob/master/docs/README_aux.md).




The classes organisation
-------------------------------

The following diagram represents how methods are distributed amongst the classes, and how classes are related 
to each other.


```

BullSheetGenerator   // pure data layer
+ void          setDir ( str:dir ) 
+ string        getPureData ( str|array:domain=null ) 


AuthorBullSheetGenerator extends BullSheetGenerator   // it adds a generated data layer
+ bool          boolean ( int:chanceOfGettingTrue=50 )
+ string        password ( int:length=10 )              // use the asciiChars method under the hood
+ string        numbers ( int:length=3 )
+ string        letters ( int:length=3 )
+ string        alphaNumericChars ( int:length=3 )      // a-z A-Z 0-9
+ string        wordChars ( int:length=3 )              // a-z A-Z 0-9 _  
+ string        asciiChars ( int:length=3 )             // from ascii code 32 (space) to 126 (~)


LingBullSheetGenerator extends AuthorBullSheetGenerator   // add a combined data layer

(combined layer data)
+ string        email ()
+ string        pseudo ( bool:useGenerator=true )       // using generator creates a lot more randomness
(pure data sugar)
+ string        actor ()
+ string        firstName ()
+ string        lastName ()
+ string        topLevelDomain ()




```




Related
---------------

- find more about [BullSheet conception](https://github.com/lingtalfi/BullSheet/blob/master/docs/README_aux.md)
- the official [bullsheets repository](https://github.com/bullsheet/bullsheets-repo)




History Log
------------------
    
- 1.0.0 -- 2016-02-10

    - initial commit
    
    
