<?php
/**
 */
class _str
{
	static public function aid($s='')
	{
        return substr(md5(microtime().$s), 0, 12);
    }
}