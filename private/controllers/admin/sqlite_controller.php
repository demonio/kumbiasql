<?php
/**
 */
class SqliteController extends AdminController
{
    #
    public function __call($name, $params)
    {
        $this->index($name, ...$params);
    }

    #
    public function index($table=null, $id=null)
    {
        if ( ! $table) {
            $this->tables = (new LiteRecord)->tables();
            return View::select('tables');
        }
        if ($id) {
            $this->row = (new $table)->get($id);
            return View::select('row');
        }
        $this->cols = (new LiteRecord)->table($table)->cols();
        $this->rows = (new LiteRecord)->table($table)->rows();
        $this->table = $table;
        View::select('rows');
    }

    #
    public function table($table=null)
    {
        if (Input::post('action') == 'create') {
            (new LiteRecord)->table(Input::post('name'))->addTable();
            return Redirect::to('/admin/sqlite/' . Input::post('name'));
        }
        else if (Input::post('action') == 'update') {
            (new LiteRecord)->table($table)->renameTable(Input::post('name'));
            return Redirect::to('/admin/sqlite/' . Input::post('name'));
        }
        else if (Input::post('action') == 'delete') {
            (new LiteRecord)->table($table)->deleteTable();
            return Redirect::to('/admin/sqlite');
        }

        $this->rows = (new LiteRecord)->table($table)->cols('all');
        $this->table = $table;
    }

    #
    public function field($table=null, $field=null)
    {
        if ( ! $table) {
            return Redirect::to('/admin/sqlite');
        }

        if (Input::post('action') == 'create') {
            (new LiteRecord)->table($table)->addField(Input::post());
        }
        elseif (Input::post('action') == 'update') {
            (new LiteRecord)->table($table)->updField(Input::post());
            return Redirect::to("/admin/sqlite/field/$table/" .  Input::post('name'));
        }
        elseif (Input::post('action') == 'delete') {
            (new LiteRecord)->table($table)->delField(Input::post());
            return Redirect::to("/admin/sqlite/field/$table");
        }
        $this->field = $field;
        $this->rows = (new LiteRecord)->table($table)->cols('all');
        $this->table = $table;
    }

    #
    public function row($table=null, $id=null)
    {
        if ( ! $table) {
            return Redirect::to('/admin/sqlite');
        }

        if (Input::post('action') == 'create') {
            (new LiteRecord)->table($table)->add(Input::post());
        }
        else if (Input::post('action') == 'update') {
            (new LiteRecord)->table($table)->upd(Input::post());
        }
        else if (Input::post('action') == 'delete') {
            (new LiteRecord)->table($table)->del($id);
        }
        if (Input::post('action')) {
            return Redirect::to("/admin/sqlite/$table");
        }

        $this->cols = (new LiteRecord)->table($table)->cols();
        $this->row = (new LiteRecord)->table($table)->row('id=?', $id);
        $this->rows = (new LiteRecord)->table($table)->rows();
        $this->table = $table;
    }
}
