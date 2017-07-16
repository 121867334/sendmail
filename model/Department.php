<?php
/**
 * 模型
 * @author FnCMS
 * @time 2011-9-6 15:33
 * @version 1.0
 */
class Department_Model extends Model {
	
	/**
	 * initModel
	 * @param mixed 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->setTable('#@_department','dep_id');
	}
	public $join = '';
	/**
	 * 按条件查找
	 * @param
	 * @return Array
	 */
	public function search($where=array(),$field='*',$limit='0, 10', $order = 'dep_id DESC'){
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
					->field("count(dep_id) as counter")
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
				->where("dep_id='".$id."'")
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
	
	public function getDepID($where,$type="str")
	{
		$rs = $this->db->table($this->table)
				->field('dep_id')
				->where($where)
				->getAll();
		$dep_id = '';
		if($rs){
			foreach ($rs as $v)
			{
				$dep_id .= $dep_id?','.$v['dep_id']:$v['dep_id'];
			}
		}
		if($type == 'arr')$dep_id = explode(',', $dep_id);
		return $dep_id;
	}
	
	public function setCache($type=1)
	{
		$rs = $this->db->table($this->table)
					->field("*")
					->where("dep_status=1")
					->getAll();
		$list = array(1=>array(),2=>array());
		if($rs)
		{
			foreach ($rs as $v)
			{
				if(!$v['dep_pid'])
				{
					$list[1][0][$v['dep_id']] = $v;
				}
				else
				{
					$list[1][$v['dep_pid']][$v['dep_id']]= $v;
				}
				$list[2][$v['dep_id']] = $v;
			}
		}
		$this->cache->setToFile('department/1', $list[1]);
		$this->cache->setToFile('department/2', $list[2]);
		return $list[$type];
	}
	public function get($type=1)
	{
		$list = $this->cache->getFromFile('department/'.$type);
		if (!$list)
		{
			$list = $this->setCache($type);
		}
		return $list;
	}
	
	public function getArrayByParentID($parent_id=0)
	{
		$rs = $this->get();
		if(!$this->id_arr)$this->id_arr[] = $parent_id;
		if($rs[$parent_id])
		{
			foreach ($rs[$parent_id] as $v)
			{
				$this->id_arr[] = $v['dep_id'];
				$this->getArrayByParentID($v['dep_id']);
			}
		}
		return $this->id_arr;
	}
	public function getTreeSelect($parent_id=0,$selected_id=0,$name='dep[dep_id]',$id='dep_id')
	{
		$select = '<select name="'.$name.'" id="'.$id.'">';
		$select .= '<option value="">部门</option>';
		$select .= $this->getOptionByParentID($parent_id,$selected_id,$name,$id);
		$select .= '</select>';
		return $select;
	}
	public function getOptionByParentID($parent_id=0,$selected_id=0,$name='dep[dep_id]',$id='dep_id')
	{
		$rs = $this->get();
		if($rs[$parent_id])
		{
			foreach ($rs[$parent_id] as $v)
			{
				$selected = '';
				if($selected_id == $v['dep_id'])$selected = " selected ";
				$str = "";
				for($i=0;$i<$v['dep_level'];$i++)
				{
					$str .= "　";
				}
				$str .= "├─";
				$select .= '<option value="'.$v['dep_id'].'" '.$selected.'>'.$str.$v['dep_name'].'</option>';
				$select .= $this->getOptionByParentID($v['dep_id'],$selected_id,$name,$id);
			}
		}
		return $select;
	}
	public function getTreeTable($where=array(),$class="table")
	{
		if($where)$this->dep_id = $this->getDepID($where,'arr');
		$tb = '<table width="100%" cellpadding="3" cellspacing="0" class="'.$class.'">'."\n";
		$tb .= '<tr>';
		$tb .= '<th>部门名称</th>';
		$tb .= '<th>部门编号</th>';
		$tb .= '<th>部门负责人</th>';
		$tb .= '<th>状态</th>';
		$tb .= '<th>操作</th>';
		$tb .= '</tr>'."\n";
		$tb .= $this->getTrByParentID(0,($where?true:false));
		$tb .= '</table>';
		return $tb;
	}
	public function getTrByParentID($parent_id=0,$dep_id=false)
	{
		$where['dep_pid'] = $parent_id;
		$rs = $this->db->table($this->table)
					->field("*")
					->where($where)
					->order("dep_id asc")
					->getAll();
		if($rs)
		{
			$user_model = load_model('User');
			$conf_model = load_model('Config');
			foreach ($rs as $v)
			{
				$bln = true;
				if($dep_id)
				{
					if(!in_array($v['dep_id'],$this->dep_id))$bln = false;
				}
				if($bln)
				{
					$user = array();
					if($v['dep_leader'])$user = $user_model->get($v['dep_leader']);
					$str = "";
					if($v['dep_level'] > 1)
					{
						for($i=1;$i<$v['dep_level'];$i++)
						{
						$str .= "　　";
						}
						$str .= "├─";
					}
					$tb .= '<tr class="list_cls list_cls_'.$v['dep_level'].'">';
					$tb .= '<td>'.$str.$v['dep_name'].'</td>';
					$tb .= '<td>'.$v['dep_no'].'</td>';
					$tb .= '<td>'.$user['use_name'].'('.$user['use_no'].')</td>';
					$tb .= '<td><img src="'.Fn::$config['web_path'].'style/'.Fn::$config['template_skin'].'/images/'.$v['dep_status'].'.gif" /></td>';
					$tb .= '<td>
								<a href="index.php?c=Department&a=add&dep_id='.$v['dep_id'].'">修改</a> | 
								<a href="index.php?c=Department&a=add&dep_pid='.$v['dep_id'].'">添加下级部门</a>
							</td>';
					$tb .= '</tr>'."\n";
				}
				$tb .= $this->getTrByParentID($v['dep_id'],$dep_id);
			}
		}
		return $tb;
	}
	public function getTreeAuth($rol_auth_dep_id=array(),$name='data[rol_auth][dep_id][]',$class='rol_auth_dep_id_cls')
	{
		//$tb = '<ul class="auth_dep_class">'."\n";
		$tb .= $this->getAuthRowByParentID(0,$rol_auth_dep_id,$name,$class);
		//$tb .= '</ul>';
		return $tb;
	}
	public function getAuthRowByParentID($parent_id=0,$rol_auth_dep_id=array(),$name='data[rol_auth][dep_id][]',$class='rol_auth_dep_id_cls')
	{
		$rs = $this->db->table($this->table)
					->field("*")
					->where("dep_status=1 and dep_pid='".$parent_id."'")
					->order("dep_id asc")
					->getAll();
		if($rs)
		{
			foreach ($rs as $v)
			{
				if($v['dep_level'] == 1)
				{
					$tb .= '<ul class="auth_dep_class">'."\n";
				}
				$str = "";
				if($v['dep_level'] > 1)
				{
					for($i=1;$i<$v['dep_level'];$i++)
					{
					$str .= "　　";
					}
					$str .= "├─";
				}
				$checked = '';
				if(in_array($v['dep_id'],$rol_auth_dep_id))$checked = 'checked';
				$tb .= '<li class="dep_cls dep_cls_'.$v['dep_level'].'">'.$str.'<input type="checkbox" name="'.$name.'" class="'.$class.'" '.$checked.' value="'.$v['dep_id'].'" />'.$v['dep_name'].'('.$v['dep_no'].')</li>'."\n";
				$tb .= $this->getAuthRowByParentID($v['dep_id'],$rol_auth_dep_id,$name,$class);
				if($v['dep_level'] == 1)
				{
					$tb .= '</ul>'."\n";
				}
			}
		}
		return $tb;
	}
}