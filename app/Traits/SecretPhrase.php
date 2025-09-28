<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

trait SecretPhrase
{
    // create secret phrase
    protected function createSecretPhrase(){
       $words = [
        'apple', 'river', 'stone', 'sky', 'cloud', 'forest', 'light', 'star', 'dream', 'fire',
        'city', 'united', 'life', 'water', 'earth', 'wind', 'mountain', 'tree', 'sun', 'moon',
        'ocean', 'bird', 'flower', 'grass', 'sand', 'storm', 'peace', 'hope', 'energy', 'sound',
        'gate', 'path', 'bridge', 'circle', 'echo', 'vision', 'soul', 'flame', 'night', 'day',
        'wolf', 'lion', 'tiger', 'eagle', 'dragon', 'shadow', 'rain', 'snow', 'storm', 'wave',
        'iron', 'silver', 'gold', 'crystal', 'dreamer', 'wander', 'hunter', 'seeker', 'keeper', 'guardian',
        'valley', 'meadow', 'desert', 'island', 'cave', 'cliff', 'peak', 'horizon', 'dawn', 'dusk',
        'breeze', 'frost', 'mist', 'fog', 'rainbow', 'lightning', 'thunder', 'twilight', 'galaxy', 'comet',
        'oak', 'pine', 'willow', 'maple', 'rose', 'lily', 'orchid', 'violet', 'daisy', 'sunflower',
        'butterfly', 'dolphin', 'whale', 'rabbit', 'deer', 'bear', 'fox', 'owl', 'hawk', 'sparrow',
        'ancient', 'eternal', 'mystic', 'secret', 'hidden', 'forgotten', 'wisdom', 'knowledge', 'truth', 'legend',
        'journey', 'quest', 'adventure', 'discovery', 'mystery', 'puzzle', 'riddle', 'secret', 'code', 'key',
        'temple', 'castle', 'tower', 'palace', 'cottage', 'cabin', 'sanctuary', 'haven', 'refuge', 'paradise',
        'courage', 'honor', 'loyalty', 'justice', 'freedom', 'strength', 'power', 'magic', 'spirit', 'essence',
        'melody', 'harmony', 'rhythm', 'song', 'voice', 'whisper', 'silence', 'calm', 'serene', 'tranquil',
        'fountain', 'spring', 'stream', 'lake', 'pond', 'waterfall', 'glacier', 'ice', 'coral', 'pearl',
        'amber', 'jade', 'ruby', 'diamond', 'emerald', 'sapphire', 'opal', 'quartz', 'topaz', 'amethyst',
        'phoenix', 'griffin', 'unicorn', 'pegasus', 'kraken', 'sphinx', 'centaur', 'fairy', 'elf', 'wizard',
        'explorer', 'pioneer', 'trailblazer', 'visionary', 'prophet', 'oracle', 'sage', 'mentor', 'apprentice', 'scholar',
        'constellation', 'universe', 'infinity', 'cosmos', 'nebula', 'planet', 'orbit', 'gravity', 'lightyear', 'quantum'
    ];
        $randomWords = collect($words)->random(11)->values()->all();
        $randomPhraseString = implode(' ', $randomWords);
        $secretPhraseHash = Crypt::encryptString($randomPhraseString);
        return $secretPhraseHash;
    }

}
