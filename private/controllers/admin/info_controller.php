<?php
/**
 */
class InfoController extends AdminController
{
    #
    public function php()
    {
        phpinfo();
        View::select(null, null);
    }
}
