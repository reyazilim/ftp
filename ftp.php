<?php

class ftp{
	private $server_host 		= NULL;
	private $server_username 	= NULL;
	private $server_password 	= NULL;
	private $server_dir 		= NULL;
	
	public  $server_sub_dir         = NULL;
	private $connect_id 		= NULL;
	private $connect_status 	= false;

	function __construct($params){
		$this->server_host = $params['host'];
		$this->server_username = $params['username'];
		$this->server_password = $params['password'];
		$this->server_dir = $params['dir'];

		@$this->connect_id = ftp_connect($this->server_host);
		@$login = ftp_login($this->connect_id,$this->server_username,$this->server_password);
		if((!$this->connect_id) || (!$login)){
			$this->connect_status = false;
		}else{
			$this->connect_status = true;
		}
		
	}

	function __destruct(){
		@ftp_close($this->connect_id);
	}

	public function file_upload($local_file, $remote_file){
		if($this->connect_status){
                    $server_dir = $this->server_dir;
                    
                    if($this->server_sub_dir!=NULL){
                        $server_dir .= $this->server_sub_dir;
                        $path = "";                  
                        $dir=explode("/", $server_dir); 
                        for ($i=0;$i<count($dir);$i++){
                            $path.="/".$dir[$i];
                            if(!@ftp_chdir($this->connect_id,$path)){
                                @ftp_chdir($this->connect_id,"/");
                                @ftp_mkdir($this->connect_id,$path);
                            }
                        }
                    }
                    @$upload = ftp_put($this->connect_id,$server_dir.$remote_file , $local_file, FTP_BINARY);
                    if(!$upload){
                        return false;
                    }else{
                        return true;
                    }
		}
	}

	public function file_rename($file, $new_name){
            if($this->connect_status){
                @$rename = ftp_rename($this->connect_id, $this->server_dir.$this->server_sub_dir.$file, $this->server_dir.$this->server_sub_dir.$new_name);
                if($rename){
                    return true;
                }else{
                    return false;
                }
            }
	}

	public function file_delete($file){
            if($this->connect_status){
                @$delete = ftp_delete($this->connect_id, $this->server_dir.$this->server_sub_dir.$file);
                if($delete){
                    return true;
                }else{
                    return false;
                }
            }
	}

        public function file_get($local_dir,$remote_file){
            if($this->connect_status){
                @$get = ftp_get($this->connect_id, $local_dir.$remote_file, $this->server_dir.$this->server_sub_dir.$remote_file, FTP_BINARY);
                if($get){
                    return true;
                }else{
                    return false;
                }
            }
	}
        
        public function ftp_list(){
            if($this->connect_status){
                @$get = ftp_nlist($this->connect_id, $this->server_dir.$this->server_sub_dir);
                if($get){
                    foreach($get as $file){
                        $files[] = iconv("ISO-8859-1","UTF-8", $file);
                        //$files[] = $file;
                    }
                    return $files;
                }else{
                    return false;
                }
            }
	}



}
