<?php
use Kumbia\ActiveRecord\LiteRecord as ORM;
/**
 */
class LiteRecord extends ORM
{
	# 1
	public function tables()
    {
		$sql = "SELECT name FROM sqlite_master WHERE type='table'";
		$tables = self::all($sql);
		/*foreach ($tables as &$table) {
            $table->fields = self::fields($table->name);
		}*/
		return $tables;
	}

	# 2
	public function select()
    {
		$fields = $this->fields ?? '*';

		$this->sql = "SELECT $fields FROM $this->table_";

		$this->sql .= empty($this->where) ? '' : $this->where;

		$this->sql .= empty($this->order) ? '' : $this->order;

		$this->sql .= empty($this->limit) ? '' : $this->limit;
	}

	# 2.1
	public function fields($fields)
    {
		$this->fields = $fields;
		return $this;
	}

	# 2.2
	public function table($table)
    {
		$this->table_ = $table;
		return $this;
	}

	# 2.3
	public function where($where)
    {
		$this->where = $where ? " WHERE $where" : '';
		return $this;
	}

	# 2.4
	public function vals($vals=[])
    {
		$this->vals = $vals;
		return $this;
	}

	# 2.5
	public function order($order)
    {
		$this->order = $order ? " ORDER BY $order" : '';;
		return $this;
	}

	# 3
	public function limit($limit)
    {
		$this->limit = $limit ? " LIMIT $limit" : '';;
		return $this;
	}

	# 4
	public function row($by=null, $val=null)
    {
		if ($by) {
			$this->where($by);
            $this->vals([$val]);
		}

		$this->select();
		$row = empty($this->vals)
			? self::first($this->sql)
            : self::first($this->sql, $this->vals);

		return empty($row) ? self::cols() : $row;
	}

    # 4.1
	public function cols($how=null)
	{
		$cols = parent::all("PRAGMA table_info($this->table_)");
		if ($how === 'all') {
			return $cols;
		}
		$arr = [];
		foreach ($cols as $col) {
			$arr[$col->name] = '';
		}
		return (object)$arr;
	}

	# 5
	public function rows($by='', $test=0)
    {
		$this->select();
		if ($test) _var::die($this);
		$rows = empty($this->vals)
			? self::all($this->sql)
			: self::all($this->sql, $this->vals);
		if ( ! $rows) {
			return [self::cols()];
		}
		if ($by) {
			$rows = self::arrayBy($rows, $by);
		}
		return $rows;
	}

    # 5.1
	public static function arrayBy($arr_old, $field='idu')
	{
        $arr_new = [];
        foreach ($arr_old as $obj) {
            $arr_new[$obj->$field] = $obj;
        }
		return $arr_new;
	}

	# 6
	public function add($post, $by='id')
    {
		unset($post[$by], $post['action']);

		foreach ($post as $field=>$value) {
			$fields[] = $field;
			$values[] = '?';
			$this->vals[] = $value;
		}

		$this->sql = "INSERT INTO $this->table_ (".implode(', ', $fields).') VALUES ('.implode(', ', $values).')';

		#_var::die($this);

		empty($this->vals)
			? self::query($this->sql)
            : self::query($this->sql, $this->vals);	
	}

	# 7
	public function upd($post, $by='id')
    {
		$field_by = $post[$by];
		unset($post[$by], $post['action']);

		foreach ($post as $field=>$value) {
			$fields[] = "$field=?";
			$this->vals[] = $value;
		}

		$this->where("$by=?");
		$this->vals[] = $field_by;

		$this->sql = "UPDATE $this->table_ SET " . implode(', ', $fields);

		if ( ! empty($this->where)) {
			$this->sql .= $this->where;
		}

		#_var::die($this);

		empty($this->vals)
			? self::query($this->sql)
            : self::query($this->sql, $this->vals);	
	}

	# 9
	/*public function sav()
    {
		empty($this->where) ? $this->add() : $this->upd();
	}*/

	# 9
	public function del($val=null, $by='id')
    {
		$this->sql = "DELETE FROM $this->table_";

		if ($val) {
			$this->sql .= " WHERE $by=?";
			$this->vals = [$val];
		}
		elseif ( ! empty($this->where)) {
			$this->sql .= $this->where;
		}

		empty($this->vals)
			? self::query($this->sql)
            : self::query($this->sql, $this->vals);	
	}

	# 10
	public function renameTable($name)
    {
		$this->sql = "ALTER TABLE $this->table_ RENAME TO $name";
		self::query($this->sql);
	}

	# 11
	public function deleteTable()
    {
		$this->sql = "DROP TABLE $this->table_";
		self::query($this->sql);
	}

	# 12
	public function addTable()
    {
		$this->sql = "CREATE TABLE $this->table_ (
			id INTEGER PRIMARY KEY AUTOINCREMENT
		)";
		self::query($this->sql);
	}

	# 13
	public function addField($post)
    {
		$this->sql = "ALTER TABLE $this->table_ ADD COLUMN {$post['name']} {$post['type']} {$post['null']} {$post['key']}";

		$this->sql .= empty($post['default'])
			? '' : " DEFAULT {$post['default']}";

		$this->sql .= empty($post['check'])
			? '' : " CHECK ({$post['check']})";

		$this->sql .= empty($post['collate'])
			? '' : " COLLATE {$post['collate']}";

		#_var::die($this);
		self::query($this->sql);

		if ($post['index']) {
			$this->sql = "CREATE INDEX {$post['index']} ON $this->table_({$post['name']})";
			self::query($this->sql);
		}
	}

	# 14 
	public function updField($post)
    {
		$this->sql = "ALTER TABLE $this->table_ RENAME COLUMN {$post['name_old']} TO {$post['name']}";
		self::query($this->sql);
	}

	# 15 
	public function delField($post)
    {
		$this->sql = "ALTER TABLE $this->table_ DROP COLUMN {$post['name']}";
		self::query($this->sql);
	}

	# 16
	public function test()
    {
		$this->sql = "SELECT * FROM sqlite_master";
		$rows = self::all($this->sql);
        _var::die($rows);
	}
}
