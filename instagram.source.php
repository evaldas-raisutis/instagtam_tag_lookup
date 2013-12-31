<?php 

class Instagram implements Source {

	// Client id used when accessin API
	private $client_id;

	// Array of tags used to filter content.
	private $tags = array();

	// Api endpoint as url
	private $tag_endpoint_url;

	// Store results in array
	private $data = array();

	public function __construct( $default_client_id = '' , array $default_tags = array() ) 
  	{
        $this->client_id = $default_client_id;
        $this->tags = $default_tags;
        $this->source = get_class( $this );
        if( !$this->is_valid_source() )
        {
        	exit(get_class( $this ) . " class does not validate as a Source");
        }
    }

	public function ini_lookup()
	{
		foreach ( $this->tags as $key => $tag ) 
		{
			$this->set_url( $tag['title'] );
			$this->get_by( $tag );
		}
		return $this->data;
	}

	public function get_by( $tag )
	{
		$continue = true;

		while( $continue )
		{
			try {
				$raw = file_get_contents( $this->tag_endpoint_url );
				$json = json_decode( $raw, true );

				if( isset( $json['pagination']['next_max_tag_id'] ) )
				{
					$this->set_url( $tag['title'], $json['pagination']['next_max_tag_id'] );
				}
				else
				{
					$continue = false;
				}

				foreach ( $json['data'] as $key => $value ) {
					// array_push( $this->data, $value ); // return raw
					array_push( $this->data, $this->process_json( $value, $tag ) ); // return processed
				}
				unset( $json );

			} catch ( Exception $e ) {
				$continue = false;
			}
		}

		return true;
	}

	public function process_json( $value, $tag )
	{
		$processed_value = array();
		if( !is_string( $value['id'] ) )
		{
			settype( $value['id'], 'string' );
		}
		$processed_value['id'] = $value['id'];
		$processed_value['type'] = $value['type'];
		$processed_value['tag'] = $tag;
		$processed_value['link'] = $value['link'];
		$processed_value['thumbnail'] = $value['images']['thumbnail'];
		$processed_value['thumbnail']['type'] = 'thumbnail';
		$processed_value['source'] = $this->source;
		if( $processed_value['type'] == 'video' )
		{
			$processed_value['full'] = $value['videos']['standard_resolution'];
		}
		else
		{
			$processed_value['full'] = $value['images']['standard_resolution'];
		}		
		$processed_value['full']['type'] = 'full';
		return $processed_value;
	}	

    public function is_valid_source()
    {
    	if( !isset( $this->client_id ) || empty( $this->client_id ) )
    	{
    		return false;
    	}
    	if( !isset( $this->tags ) || empty( $this->tags ) )
    	{
    		return false;
    	}    	

    	return true;
    }

    public function set_url( $tag, $max_id = false )
    {
    	if( !$max_id )
    	{
    		$this->tag_endpoint_url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?client_id=" . $this->client_id;
    	}
    	else 
    	{	
    		$this->tag_endpoint_url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?client_id=" . $this->client_id . "&max_tag_id=" . $max_id;
    	}
 
    }  	

}
 ?>
