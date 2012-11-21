<?php

/**
 * Session storage in MySql database
*/
class MySQLStorage implements \Nette\Http\ISessionStorage {
	/** @var \Nette\Database\Connection */
	protected $oDb = null;
	
	/** @var string */
	protected $sSavePath = null;
	
	/** @var \Nette\Database\Table */
	protected $oTable = null;
	
	/** @var string */
	protected $sSessionName = null;

	/** open the storage talbe
	 * @author Tomas Jancik
	 */
	public function open($sSavePath, $sessionName) {
		if(is_null($this->oDb)) {
			Throw new RuntimeException(__METHOD__ . ' called before proper database was set');
		}
		$this->oTable = $this->oDb->table($sSavePath);
		$this->sSessionName = $sessionName;
		
		return true;
	}

	public function close() {
		$this->oTable = null;
		return true;
	}

	public function read($id) {
		$aData = $this->oTable->select('data')->where('id', $id)->order('timestamp DESC')->fetch();
		return $aData['data'];
	}

	public function write($id, $data) {
		$this->oTable->where('id', $id)->delete();
		$this->oTable->insert(array('id' => $id, 'data' => $data, 'timestamp' => date('Y-m-d H:i:s')));
		return true;
	}

	public function remove($id) {
		$this->oTable->where('id', $id)->delete();
		return true;
	}

	public function clean($maxlifetime) {
		$this->oTable->where('timestamp  < ?', date('Y-m-d H:i:s', time() - $maxlifetime))->delete();
		return true;
	}
	
	public function setDatabase(\Nette\Database\Connection $oDatabase) {
		$this->oDb = $oDatabase;
		return true;
	}
}
