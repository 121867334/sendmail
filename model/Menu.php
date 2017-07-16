<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Menu_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_menu','men_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'men_id DESC'){
		$rs = $this->db->table($this->table)
					->field($field)
					->where($where)
					->limit($limit)
					->order($order)
					->getAll();
		return $rs;
	}
	public function getTotalNum($where=array()){
		$rs = $this->db->table($this->table)
					->field("count(men_id) as counter")
					->where($where)
					->getOne();
		return intval($rs['counter']);
	}
	
	/**
	 * 按att_id查找
	 * @param
	 * @return Array
	 */
	public function getById($id,$field='*'){
		$rs = $this->db->table($this->table)
				->field($field)
				->where("use_id='".$id."'")
				->getOne();
		return $rs;
	}
	public function getOne($where){
		if(!$where)return false;
		$rs = $this->db->table($this->table)
				->field("*")
				->where($where)
				->getOne();
		return $rs;
	}
	public function setByWhere($where,$data)
	{
		if(!$where)return false;
		return $this->db->table($this->table)->where($where)->update($data);
	}
	
	public function setCache($type)
	{
		$rs = $this->db->table($this->table)
					->field('*')
					->where("men_status=1")
					->order('men_sort asc,men_id asc')
					->getAll();
		$menu = array(1=>array(),2=>array());
		if($rs){
			foreach ($rs as $v)
			{
				if(!$v['men_pid'])
				{
					$menu[1][$v['men_id']] = $v;
				}
				else
				{
					$menu[1][$v['men_pid']]['list'][$v['men_id']] = $v;
				}
				$menu[2][(($v['men_c']||$v['men_a'])?$v['men_c'].'_'.$v['men_a']:$v['men_id'])] = $v;
				$menu[3][$v['men_c']] = $v;
			}
		}
		$this->cache->setToFile('menu/1', $menu[1]);
		$this->cache->setToFile('menu/2', $menu[2]);
		$this->cache->setToFile('menu/3', $menu[3]);
		return $menu[$type];
	}
	public function get($type='1')
	{
		$this->menu = $this->cache->getFromFile('menu/'.$type);
		if (!$this->menu)
		{
			$this->menu = $this->setCache($type);
		}
		return $this->menu;
	}
	public function getTreeAuth($rol_auth_men_id=array(),$name='data[rol_auth][men_id][]',$class='rol_auth_men_id_cls')
	{
		//$tb = '<ul class="auth_men_class">'."\n";
		$tb .= $this->getAuthRowByParentID(0,$rol_auth_men_id,$name,$class);
		//$tb .= '</ul>';
		return $tb;
	}
	public function getAuthRowByParentID($parent_id=0,$rol_auth_men_id=array(),$name='data[rol_auth][men_id][]',$class='rol_auth_men_id_cls')
	{
		$rs = $this->db->table($this->table)
					->field("*")
					->where("men_status=1 and men_pid='".$parent_id."'")
					->order("men_id asc")
					->getAll();
		if($rs)
		{
			foreach ($rs as $v)
			{
				if($v['men_level'] == 1)
				{
					$tb .= '<ul class="auth_men_class">'."\n";
				}
				$str = "";
				if($v['men_level'] > 1)
				{
					for($i=1;$i<$v['men_level'];$i++)
					{
					$str .= "　　";
					}
					$str .= "├─";
				}
				$checked = '';
				if(in_array($v['men_id'],$rol_auth_men_id))$checked = 'checked';
				$tb .= '<li class="men_cls men_cls_'.$v['men_level'].'">'.$str.'<input type="checkbox" name="'.$name.'" class="'.$class.'" '.$checked.' value="'.$v['men_id'].'" />'.$v['men_name'].'</li>'."\n";
				$tb .= $this->getAuthRowByParentID($v['men_id'],$rol_auth_men_id,$name,$class);
				if($v['men_level'] == 1)
				{
					$tb .= '</ul>';
				}
			}
		}
		return $tb;
	}
}