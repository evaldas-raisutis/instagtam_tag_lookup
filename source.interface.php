<?php 

Interface Source {

  	public function __construct();

  	public function ini_lookup();

	public function get_by( $tag );

	public function process_json( $value, $tag );

    public function is_valid_source();

    public function set_url( $tag, $max_id = false );    

}
 ?>
