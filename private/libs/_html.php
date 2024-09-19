<?php
/**
 */
class _html
{
    # 1
    public static function tag($tag, $content='', $args=[])
    {
        return self::openTag($tag, $args) . $content . self::closeTag($tag);
    }
    
    # 1.1
    public static function openTag($tag, $args=[])
    {
        $params = self::arrayToStr($args);
        return "<$tag$params>";
    }

    # 1.1.1
    private static function arrayToStr($array)
    {
        if ( ! $array) {
            return '';
        }
        $str = '';
        foreach ($array as $key=>$val) {
            $str .= empty($val) ? " $key" : " $key=\"$val\"";
        }
        return $str;
    }
    
    # 1.2
    public static function closeTag($tag)
    {
        return "</$tag>";
    }

    # 2
    public static function button($content, $args=[])
    {
        if ( ! strstr($content, '<')) {
            $span['class'] = 'truncate';
            $content = self::tag('span', $content, $span);
        }

        $args['type'] = $args['type'] ?? 'button';
        return self::tag('button', $content, $args);
    }

    # 3
    public static function link($type, $href, $content='', $args=[])
    {   
        if ($type == 'button') $args['role'] = 'button';
        $args['href'] = $href;
        return self::tag('a', $content, $args);
    }

    # 4
    public static function img($dir, $file, $width='', $height='', $alt='', $args=[])
    {
        if (stristr($file, '.mp4')) {
            return self::mp4($file, $args);
        }

        $args['alt'] = empty($alt)
            ? ucfirst(trim(str_replace(['/', '-', '_', '.'], ' ', $file)))
            : $alt;        
        $args['loading'] = $args['loading'] ?? 'lazy';
        $args['src'] = "$dir/$file";
        if ($width) {
            $args['width'] = $width;
        }
        if ($height) {
            $args['height'] = $height;
        }
        if ($width || $height) {
            return self::openTag('img', $args);
        }

        if ( ! stristr($file, '.svg')) {
            $args['sizes'] = "(max-width: 575.98px) 320px, 
            (min-width: 576px) and (max-width: 767.98px) 576px, 
            (min-width: 768px) and (max-width: 991.98px) 768px, 
            (min-width: 992px) and (max-width: 1199.98px) 992px, 
            (min-width: 1200px) and (max-width: 1399.98px) 1200px, 
            1400px";
            $args['srcset'] = "$dir/320w.$file 320w, 
            $dir/576w.$file 576w, 
            $dir/768w.$file 768w, 
            $dir/992w.$file 992w, 
            $dir/1200w.$file 1200w, 
            $dir/1400w.$file 1400w, 
            $dir/1920w.$file 1920w";
        }

        return self::openTag('img', $args);
    }

    # 4.1
    public static function mp4($src, $args=[])
    {
        $source['src'] = $src;
        $source['type'] = 'video/mp4';
        $content = self::openTag('source', $source);

        $args['autoplay'] = '';
        $args['loop'] = '';
        $args['muted'] = '';
        $args['playsinline'] = '';
        return self::tag('video', $content, $args);
    }
}
