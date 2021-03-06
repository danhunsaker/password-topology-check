<?php

namespace DanHunsaker\PasswordTopology;

/**
 * @author		Nathanael Padgett (417solutions.com)
 * @copyright   2015 by 417 Solutions
 * @author		Dan Hunsaker <danhunsaker@gmail.com>
 * @copyright   2018 by Dan Hunsaker
 */
class Topology
{
    protected static $unicode = true;

    /**
     * @var array The list of forbidden topologies
     *
     * The default list here was pulled from
     * http://blog.korelogic.com/blog/2014/04/04/pathwell_topologies
     */
    protected static $forbidden = [
        'dddddddd',     'ddddddul',     'ddddulll',      'ddddullll',
        'ddulllll',     'ddullllll',    'ddulllllll',    'llldddds',
        'lllldddd',     'lllldddds',    'llllddds',      'lllllddd',
        'llllldddd',    'llllldds',     'lllllldd',      'llllllddd',
        'lllllldddd',   'lllllldds',    'llllllds',      'llllllld',
        'llllllldd',    'lllllllddd',   'llllllldddd',   'llllllldds',
        'lllllllds',    'llllllll',     'lllllllld',     'lllllllldd',
        'lllllllll',    'llllllllld',   'llllllllldd',   'llllllllll',
        'lllllllllld',  'lllllllllll',  'llllllllsd',    'llllllsd',
        'llllllsdd',    'lllllsdd',     'llllsddd',      'llllsdddd',
        'lllsdddd',     'llsddlddl',    'udllllllld',    'uldddddd',
        'ullddddd',     'ulldddddd',    'ulldddds',      'ullldddd',
        'ulllddddd',    'ullldddds',    'ulllddds',      'ullllddd',
        'ulllldddd',    'ulllldddds',   'ullllddds',     'ulllldds',
        'ullllldd',     'ulllllddd',    'ullllldddd',    'ullllldddds',
        'ulllllddds',   'ullllldds',    'ulllllds',      'ulllllld',
        'ulllllldd',    'ullllllddd',   'ulllllldddd',   'ulllllldddds',
        'ulllllldds',   'ullllllds',    'ullllllld',     'ullllllldd',
        'ulllllllddd',  'ullllllldddd', 'ullllllldds',   'ulllllllds',
        'ulllllllld',   'ulllllllldd',  'ulllllllldddd', 'ullllllllld',
        'ullllllllldd', 'ulllllllsd',   'ullllllsd',     'ullllllsdd',
        'ulllllsd',     'ulllllsdd',    'ulllllsddd',    'ulllllsdddd',
        'ullllsdd',     'ullllsddd',    'ullllsdddd',    'ulllsddd',
        'ulllsdddd',    'ullsdddd',     'uudllldddd',    'uudllldddu',
        'uuullldddd',   'uuuuuudl',     'uuuuuuds',      'uuuuuuuu',
    ];

    /**
     * Convert a password/phrase to its topolgy string
     *
     * @param string $password  The password/phrase to retrieve the topology of
     * @return string           The topology string
     */
    public static function convert($password)
    {
        $pattern = "";

        for ($i = 0; $i < (static::$unicode ? mb_strlen($password) : strlen($password)); $i++) {
            $char = static::$unicode ? mb_substr($password, $i, 1) : $password[$i];

            if (preg_match(static::$unicode ? '/[^\p{N}\p{L}]/u' : '/[^0-9a-zA-Z]/', $char)) {
                // Not a number or letter
                $pattern .= 's';
            } elseif (preg_match(static::$unicode ? '/[\p{N}]/u' : '/[0-9]/', $char)) {
                // Any numeric character
                $pattern .= 'd';
            } elseif (preg_match(static::$unicode ? '/[\p{Lu}\p{Lt}]/u' : '/[A-Z]/', $char)) {
                // Any uppercase or mixed-case letter
                $pattern .= 'u';
            } elseif (preg_match(static::$unicode ? '/[^\P{L}\p{Lu}\p{Lt}]/u' : '/[a-z]/', $char)) {
                // Any letter that isn't uppercase or mixed-case
                $pattern .= 'l';
            }
        }

        return $pattern;
    }

    /**
     * Check whether a password/phrase uses an allowed topology
     *
     * @param string $password  The password/phrase to check
     * @return bool             Whether the password/phrase is allowed
     */
    public static function check($password)
    {
        return !in_array(static::convert($password), static::$forbidden);
    }

    public static function unicode($enabled = null)
    {
        if (!empty($enabled)) {
            static::$unicode = $enabled;
        }

        return static::$unicode;
    }

    /**
     * Add a topology to the forbidden list
     *
     * @param string $topology  The topology to forbid
     */
    public static function forbid($topology)
    {
        if (!preg_match('/[^dlsu]/', $topology) && !in_array($topology, static::$forbidden)) {
            static::$forbidden[] = $topology;
        }
    }

    /**
     * Remove a topology from the forbidden list
     *
     * @param string $topology  The topology to allow
     */
    public static function allow($topology)
    {
        if (($index = array_search($topology, static::$forbidden)) !== false) {
            unset(static::$forbidden[$index]);
        }
    }

    /**
     * Add a list of topologies to the forbidden list
     *
     * @param array $topologies  The topologies to forbid
     */
    public static function forbidMany($topologies)
    {
        foreach ($topologies as $topology) {
            static::forbid($topology);
        }
    }

    /**
     * Remove a list of topologies from the forbidden list
     *
     * @param array $topologies  The topologies to allow
     */
    public static function allowMany($topologies)
    {
        foreach ($topologies as $topology) {
            static::allow($topology);
        }
    }
}
