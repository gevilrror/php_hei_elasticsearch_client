# php_elasticsearch

        
        require_once dirname(__FILE__).'/php_elasticsearch.php';
        
        $params = array(
        	'hosts' => array('localhost:9200'),
        );
        $client = new php_elasticsearch($params);
        
        $params = array(
        	'body'  => array('testField' => 'abc'),
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'id'    => 'my_id',
        );
        $ret = $client->exec($params);
        var_dump($ret);
        
        
        $getParams = array(
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'id'    => 'my_id',
        	'method' => 'get',
        );
        $ret = $client->exec($getParams);
        var_dump($ret);
        
        
        
        $params = array(
            'action' => '_count',
            'index' => 'zk',
            'type'  => 'article',
        );
        $ret = $client->exec($params);
        var_dump($ret);
        
        $searchParams = array(
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'action' => '_search',
        	'body'  => array(
        		'query' => array(
        			'match' => array(
        				'testField' => 'abc',
        			),
        		),
        	),
        );
        $ret = $client->exec($searchParams);
        var_dump($ret);
        
        
        $deleteParams = array(
        	'index' => 'my_index',
        	'type' => 'my_type',
        	'id' => 'my_id',
        	'method' => 'delete',
        );
        $ret = $client->exec($deleteParams);
        var_dump($ret);
        
        
        $deleteParams = array(
        	'index' => 'my_index',
        	'method' => 'delete',
        );
        $ret = $client->exec($deleteParams);
        var_dump($ret);
        
        
        $indexParams = array(
        	'index' => 'my_index',
        	'body' => array(
        		'settings' => array(
        			'number_of_shards' => 2,
        			'number_of_replicas' => 0,
        		),
        	),
        	'method' => 'put',
        );
        $ret = $client->exec($indexParams);
        var_dump($ret);
