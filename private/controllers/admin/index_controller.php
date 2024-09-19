<?php
/**
 */
class IndexController extends AdminController
{
    #
    public function index()
    {
        Redirect::to('admin/sqlite');
    }
}
