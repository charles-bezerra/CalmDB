<?php 
	// class responsavel pelo gerenciamento do banco ( conexões, selects, inserts e verificações)
    class DB{ 
        
        protected $table;
        protected $user;
        protected $host;
        protected $password;
        protected $db;
        protected $con;

        function __construct($table){
            require "../config/config.php"; //incluindo as váriaveis de configuração
            
            $this->table = $table;
            $this->user = $user;
            $this->host = $host;
            $this->password = $password;
            $this->db = $database;
        }
        
        //Cria exceções para erros 
        public function throw_ex($er){  
  			throw new Exception($er);  
		}

		//função que cria uma conexão com o banco
        public function connect(){
            try {
                $error = "Não foi possível conectar ao banco de dados!";
                $this->con = mysqli_connect($this->host, $this->user, $this->password, $this->db) or 
                $this->throw_ex($error);

            } catch (Exception $error) {
                echo $error->getMessage();
            }
        }

        //função que fecha a conexão existente com o banco
        public function close_connect(){
        	try {
            
                $error = "Falha ao fechar a conexão com o do banco de dados \n";      
        		
        		mysqli_close($this->con) or 
        		$this->throw_ex($error);

        	} catch (Exception $error) {
            
                echo $error->getMessage();
            
            }
        }

        // Retorna uma matrix de arrays com todas as linhas de uma tabela
        public function all(){
        	$this->connect();
	        try {

	            $error = "Error de execução \n";
	            
	            $result = mysqli_query($this->con, "select * from " . $this->table) or $this->throw_ex($error);
		        
		        $this->close_connect();
		        if($result){
			        $aux = mysqli_fetch_all($result);
			        return $aux;
		        }
		        else{
		        	echo "Nada foi encontrado";
		        	return false;
		        }

	        } catch (Exception $error) {
	            	
	           	$this->close_connect();
	           	echo $error->getMessage();
	           	return false;	            
	       	}
        }
        // verifica a existencia de algum valor na coluna de uma tabela
        public function verify_for_key($column, $key){
            $this->connect();
            try{
                $error = "Error ao executar o script select";
                $query = "";
                if(is_integer($key)){
                    $query = "select * from " . $this->table . " as t where t." . $column . " = " . $key; 
                }else{
                    $query = "select * from " . $this->table . " as t where t." . $column . " = " . "'$key'";
                }
                $result = mysqli_query($this->con, $query) or $this->throw_ex($error);
                $num = mysqli_num_rows($result);
                if($num > 0){
                    return true;
                }else {
                    return false;
                }
            } catch(Exception $error){
            	echo $error->getMessage();
            	return false;
            }
            $this->close_connect();
        }

        // encontra um array(linha) de uma Tabela                
        public function find($id){
            $this->connect();
            if($id <= 0 or is_double($id) or is_string($id)){
                echo "O id teve ter um valor inteiro positivo maior do que 0 \n";
                return false;
            }
            try {
            
                $error = "Error ao executar a busca por id no banco \n";
                $error2 = "Resultado vazio";

                $result = mysqli_query($this->con, "select * from " . $this->table . " where id = " . $id) or $this->throw_ex($error);

                $this->close_connect();
                
                $num = mysqli_num_rows($result);
                
                if($num > 0){
                    try{
                        $error2 = "Por algum motivo não podemos recuperar os dados dessa tabela";
                        $aux = mysqli_fetch_all($result) or $this->throw_ex($error2);
                        return $aux[0];
                    }
                    catch (Exception $error2) {
                        echo $error2->getMessage();
                        return false;                    
                    }
                }
                else{
                    echo "ID não encontrado";
                    return false;
                }
            } catch (Exception $error) {
                
                $this->close_connect();
                echo $error->getMessage();
                return false;
            
            }

        }
        

        // função responsável pelos inserts (Tabela, as colunas que serão preenchidas, os valores a serem preenchidos)
        public function insert($columns, $values){ 
            
            $this->connect();
            $items = "";
            $column = "";
            $time = date('Y-m-d');

            try{
                $error = "Error ao executar script insert, verifique as informações que foram passadas\n";
                
                // Informando as colunas que seram preenchidas
                for($i = 0; $i < count($columns); $i++)
                {
                	if ($i == count($columns)-1) { $column = $column . " " . "`" . $columns[$i] . "`"; }
                	else{ $column = $column . " " . "`" . $columns[$i] . "`,"; }
                }

                // Informando os valores que seram preencidos
                for($i = 0; $i < count($columns); $i++)
                {
                	if(is_int($values[$i]) or is_float($values[$i])){
                		if($i == count($columns)-1)  { $items = $items . " " . $values[$i]; } 
                		else{ $items = $items . " " . $values[$i] . ","; }
                	}
                	else
                	{
                		if($i == count($columns)-1)  { $items = $items . " " . "'" . $values[$i] . "'"; } 
                		else{ $items = $items . " " . "'" . $values[$i] . "',"; }
                	}	
                }
                
                mysqli_query($this->con, "insert into " . $this->table . " (" . $column . ") values (" . $items . ")") or $this->throw_ex($error);
                $this->close_connect();
                return true;
            }
            catch(Exception $error){
            	$this->close_connect();
                echo $error->getMessage();
            	return false;
            }
        }

        // public function select($columns, $condition, $value){
        //     $this->connect();
        //     try{
        //         $select = "";
        //         for($i = 0; $i < count($columns); $i++){

        //         }
        //         mysqli_query($this->con, "");
        //     }
        //     catch(Exception $error){
        //     	$this->close_connect();
        //         echo $error->getMessage();
        //     	return false;
        //     }

        // }
        
        // Atualiza algum atributo, parametros (id, coluna, novo valor)
        public function update($id, $column, $value){
            $this->connect();
            try{
                $error = "Não foi possível atualizar";
                $query = "UPDATE " . $this->table . " SET " . $column . "=" . "'$value'" . " WHERE id=" . $id;
                mysqli_query($this->con, $query) or $this->throw_ex($error);
                $this->close_connect();    
            }
            catch(Exception $error){
            	$this->close_connect();
                echo $error->getMessage();
            	return false;
            }
        }
        
        public function delete($id){
            $this->connect();
        }
    }

    class DBnative extends DB{
        // Aqui vc pode incrementar códigos, se seu banco tiver um CRUD nativo
    }

    $Person = new DB("Person");
    $var = $Person->verify_for_key("id", 2);

    var_dump($var);
?>