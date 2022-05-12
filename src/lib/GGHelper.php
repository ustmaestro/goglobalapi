<?php

namespace ustmaestro\goglobalapi\lib;

/**
 * GoGlobal XML helper
 */
class GGHelper
{
    /**
     * Create a xml tag with/without content or attributes
     *
     * @param string $tag
     * @param string $value
     * @param array $attributes
     * @return string
     */
    public static function wrapTag(string $tag, string $value = "", array $attributes = []): string
    {
        $attr = "";
        foreach ($attributes as $k => $v) {
            $attr .= sprintf(" %s=\"%s\"", $k, $v);
        }
        if (!empty($value)) {
            return sprintf("<%s%s>%s</%s>", $tag, $attr, $value, $tag);
        } else {
            return sprintf("<%s%s/>", $tag, $attr);
        }
    }
}
