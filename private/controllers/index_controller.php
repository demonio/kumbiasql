<?php
/**
 */
class IndexController extends AppController
{
    #
    public function index()
    {
        Redirect::to('admin/sqlite');
    }
}
