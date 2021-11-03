<?php 
/**
 * 
 * DSI object for raw database functions
 * 
 */





class DSI_Db{

    /**
	 * Whether to show SQL/DB errors.
	 *
	 * Default is to show errors if both WP_DEBUG and WP_DEBUG_DISPLAY evaluate to true.
	 *
	 * @since 0.71
	 * @var bool
	 */
	

    public $con;

    
    public function __construct(){
        
        $this->dsi_db_connect();
    }
    /**
     * Insert To Database
     */
    public function dsi_db_connect(){
        $this->con = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    }
    /**
     * Insert into Database
     * 
     * @param string @table table
     * @param array $dbargs array('field'=>'values')
     */

    public function dsi_db_insert($table = '', $dbargs = array()){
        $field = "";
        $values = "";
        foreach($dbargs as $dakeys => $davalues){

            $field .= $dakeys . ",";
            
            $values .= "'" . $davalues . "',";

        }
        $field =trim($field,',');
        $values =trim($values,',');
        $args = "(";
        $args .= $field;
        $args .= ")";
        $args .= " VALUES ";
        $args .= "(";
        $args .= $values;
        $args .= ")";
        $sql = "INSERT INTO  " . $table . " " . $args ." ";
        $ins = $this->con->query($sql);
        // return $this->con->insert_id;
        // if($this->con->error == 0){
        //     $this->con->insert_id;
        // }
        // else{
        //return $ins->error;
        print_r($dbargs);
        if($this->con->error){
            echo  $this->con->error;
        }
        else{
            echo  $this->con->insert_id;
        }
    }
    /**
     * Update
     */
    public function dsi_db_update($table,$criteria,$valuargs){
        
        
    }
    public function dsi_db_delete(){
        
    }
    /**
     *  Function Get 
     *  
     * @param string $table 
     * @param array $param
     *  
     */
    public function dsi_select_query_single($table, $param = array()){
        $sql = '';
        $param_string = '';
        
        foreach($param as $pkey => $pval){
            $param_string .= $pkey . " = '" . $pval . "'";
        }

        $sql = 'SELECT * FROM '. $table .' WHERE  '. $param_string .' ';
        
        return $this->con->query($sql);
    }

    /**
     *  Function Get Many
     *  
     * @param string $table 
     * @param array $param
     *  
     */
    public function dsi_select_query_get_many($table, $param = array()){
        $sql = '';
        $param_string = '';
        $c = 0;
        foreach($param as $pkey => $pval){

            $param_string .= $pkey . " = '" . $pval . "'";
            $param_string .= (count($param) > 0 )? ' AND ' : ' ' ;
            
            $c++;
        }
        $param_string = trim($param_string,' AND ');
        $sql = 'SELECT * FROM '. $table .' WHERE  '. $param_string .' ';
        
        // return $sql;
        return $this->con->query($sql);
    }
    /**
     *  Convert the title into slug
     * @param string $post_title old post title
     */

    public function ds_convert_title_to_slug($post_title){
        //check if slug exist
        $slug = sanitize_title($post_title);
        global $wpdb;
        
        $table = $wpdb->prefix . "posts";  
        $param = [
            'post_name' => $slug
        ];

        $r =  $this->dsi_select_query_get_many($table,$param);
        if($r->num_rows > 0){
            $slug = $slug . "-" . ($r->num_rows + 1);
            $table = $wpdb->prefix . "posts";  
            $param = [
                'post_name' => $slug
            ];

            $r =  $this->dsi_select_query_get_many($table,$param);
        }
        return $slug;
        
    }
    /**
     * Will insert new data into wp_post
     */
    public function dsi_wp_create_post(){

    }
    /** 
     * Will insert Product Meta
     * 
     * @param string @product_id
     * @param string @metakey
     * @param string @metavalue
     */

    public function update_product_meta($product_id,$metakey ,$metavalue){
        global $wpdb;
        $table = $wpdb->prefix . "postmeta";// table prefix
        //check if post_id exist
        // if yes - create new postmeta
        // else - update postmeta with new filter
        $pid =  $this->dsi_select_query_get_many($table,['post_id'=>$product_id]);
        if($pid->num_rows > 0){
            
        }

    }

}

