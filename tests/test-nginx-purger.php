<?php

class NginxPurgerTest extends WP_UnitTestCase
{
	/**
	 * @test
	 */
	function nginx_purger_remove_scheme_from_url()
	{
		$url = nginx_purger_remove_scheme_from_url( 'http://example.com/' );
		$this->assertSame( 'example.com/', $url );
		$url = nginx_purger_remove_scheme_from_url( 'https://example.com/' );
		$this->assertSame( 'example.com/', $url );
	}

	/**
	 * @test
	 */
	 function nginx_purger_get_purger_url()
	 {
		 $url = nginx_purger_get_purger_url( '111.222.333.444', 'http://example.com/' );
		 $this->assertSame( 'http://111.222.333.444/purge/example.com/', $url );
		 $url = nginx_purger_get_purger_url( 'http://111.222.333.444/', 'http://example.com/' );
		 $this->assertSame( 'http://111.222.333.444/purge/example.com/', $url );
		 $url = nginx_purger_get_purger_url( 'http://server.example.com/', 'http://example.com/' );
		 $this->assertSame( 'http://server.example.com/purge/example.com/', $url );
	 }

	 /**
	  * @test
	  */
	public function nginx_purger_send_request()
	{
		$res = nginx_purger_send_request( 'http://there-is-no-server.com' );
		$this->assertTrue( is_wp_error( $res ) );
	}
}
